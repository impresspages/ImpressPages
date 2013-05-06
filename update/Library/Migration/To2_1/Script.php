<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

namespace IpUpdate\Library\Migration\To2_1;


class Script extends \IpUpdate\Library\Migration\General{

    private $conn;
    private $dbPref;

    public function process($cf)
    {
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);
        $this->conn = $conn;
        $this->dbPref = $cf['DB_PREF'];


        $this->deleteParameter('standard', 'content_management', 'widget_faq', 'title');
        $this->deleteParameter('standard', 'content_management', 'widget_faq', 'text');

        $module = $this->getModule(null, 'standard', 'content_management');

        $group = $this->getParametersGroup($module['id'], 'widget_faq');
        if ($group) {
            if(!$this->getParameter('standard', 'content_management', 'widget_faq', 'question')) {
                $this->addStringParameter($group['id'], 'Question', 'question', 'Question', 1);
            }
            if(!$this->getParameter('standard', 'content_management', 'admin_translations', 'answer')) {
                $this->addStringParameter($group['id'], 'Answer', 'answer', 'Answer', 1);
            }
        }

        $group = $this->getParametersGroup($module['id'], 'widget_contact_form');
        if ($group) {
            if(!$this->getParameter('standard', 'content_management', 'widget_contact_form', 'move')) {
                $this->addStringParameter($group['id'], 'Move', 'move', 'Move', 1);
            }
            if(!$this->getParameter('standard', 'content_management', 'widget_contact_form', 'remove')) {
                $this->addStringParameter($group['id'], 'Remove', 'remove', 'Remove', 1);
            }

            if(!$this->getParameter('standard', 'content_management', 'widget_contact_form', 'options')) {
                $this->addStringParameter($group['id'], 'Options', 'options', 'Options', 1);
            }

            if(!$this->getParameter('standard', 'content_management', 'widget_contact_form', 'send')) {
                $this->addParameter($group['id'], array('name' => 'send', 'translation' => 'Send', 'admin' => 0, 'type'=> 'lang', 'value' => 'Send', 'comment' => ''));
            }

        }

        $module = $this->getModule(null, 'standard', 'configuration');
        $group = $this->getParametersGroup($module['id'], 'main_parameters');
        if ($group) {
            if(!$this->getParameter('standard', 'configuration', 'main_parameters', 'email_title')) {
                $this->addParameter($group['id'], array('name' => 'email_title', 'translation' => 'Default email title', 'admin' => 0, 'type'=> 'lang', 'value' => 'Hi,', 'comment' => ''));
            }

        }

        $module = $this->getModule(null, 'community', 'user');
        $group = $this->getParametersGroup($module['id'], 'admin_translations');
        if ($group) {
            if(!$this->getParameter('community', 'user', 'admin_translations', 'registration')) {
                $this->addStringParameter($group['id'], 'Registration', 'registration', 'Registration', 1);
            }


        }


        $group = $this->getParametersGroup($module['id'], 'translations');
        if ($group) {
            if(!$this->getParameter('community', 'user', 'translations', 'text_registration_verified')) {
                $this->addParameter($group['id'], array('name' => 'text_registration_verified', 'translation' => 'Text - registration verified', 'admin' => 0, 'type'=> 'lang_wysiwyg', 'value' => 'Registration has been aproved. You can login now.', 'comment' => ''));
            }
        }

        $moduleGroup = $this->getModuleGroup('administrator');
        $moduleId = $this->getModuleId('administrator', 'theme');
        if ($moduleId === false) {
            $moduleId = $this->addModule($moduleGroup['id'], 'Theme', 'theme', true, true, true, '1.00');
            $users = $this->getUsers();
            foreach($users as $user){
                $this->addPermissions($moduleId, $user['id']);
            }
        }
        $parametersGroup = $this->getParameterGroup($moduleId, 'admin_translations');
        if ($parametersGroup) {
            $groupId = $parametersGroup['id'];
        } else {
            $groupId = $this->addParameterGroup($moduleId, 'admin_translations', 'Admin translations', 1);
        }
        $this->addStringParameter($groupId, 'Successful install', 'successful_install', 'New theme has been successfully installed.', 1);
        $this->addStringParameter($groupId, 'Install', 'install', 'Install', 1);
        $this->addStringParameter($groupId, 'Title', 'title', 'Choose theme', 1);

        $sql = "ALTER TABLE `".$this->dbPref."m_administrator_repository_file` ADD INDEX (  `filename` )";
        $rs = mysql_query($sql, $this->conn);
        if (!$rs) {
            throw new \IpUpdate\Library\UpdateException($sql.' '.mysql_error(), \IpUpdate\Library\UpdateException::SQL);
        }

        $rs = mysql_query("SHOW COLUMNS FROM `".$this->dbPref."m_content_management_widget` LIKE 'recreated'", $this->conn);
        $columnExists = (mysql_num_rows($rs)) ? true : false;
        if (!$columnExists) {
            $sql = "ALTER TABLE `".$this->dbPref."m_content_management_widget` ADD  `recreated` INT NOT NULL COMMENT  'when last time the images were cropped freshly' AFTER `created`";
            $rs = mysql_query($sql, $this->conn);
            if (!$rs) {
                throw new \IpUpdate\Library\UpdateException($sql.' '.mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            }
        }

        $sql = "UPDATE `".$this->dbPref."m_content_management_widget` SET recreated = created WHERE 1";
        $rs = mysql_query($sql, $this->conn);
        if (!$rs) {
            throw new \IpUpdate\Library\UpdateException($sql.' '.mysql_error(), \IpUpdate\Library\UpdateException::SQL);
        }

        if ($this->getSystemVariable('theme_changed') === false) {
            $this->insertSystemVariable('theme_changed', time());
        }


        if ($this->getSystemVariable('last_system_message_sent') === false) {
            $this->insertSystemVariable('last_system_message_sent', '');
        }

        if ($this->getSystemVariable('last_system_message_shown') === false) {
            $this->insertSystemVariable('last_system_message_shown', '');
        }


        //add developer/form module
        $moduleGroup = $this->getModuleGroup('developer');
        $moduleId = $this->getModuleId('developer', 'form');
        if ($moduleId === false) {
            $moduleId = $this->addModule($moduleGroup['id'], 'Form', 'form', false, false, true, '1.00');
            $users = $this->getUsers();
            foreach($users as $user){
                $this->addPermissions($moduleId, $user['id']);
            }
        }
        $parametersGroup = $this->getParameterGroup($moduleId, 'error_messages');
        if ($parametersGroup) {
            $groupId = $parametersGroup['id'];
        } else {
            $groupId = $this->addParameterGroup($moduleId, 'error_messages', 'Error messages', 0);
        }

        if(!$this->getParameter('developer', 'form', 'error_messages', 'unknown')) {
            $this->addParameter($groupId, array('name' => 'unknown', 'translation' => 'Unknown', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please correct this value', 'comment' => ''));
        }
        if(!$this->getParameter('developer', 'form', 'error_messages', 'email')) {
            $this->addParameter($groupId, array('name' => 'email', 'translation' => 'Email', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a valid email address', 'comment' => ''));
        }
        if(!$this->getParameter('developer', 'form', 'error_messages', 'number')) {
            $this->addParameter($groupId, array('name' => 'number', 'translation' => 'Number', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a valid numeric value', 'comment' => ''));
        }
        if(!$this->getParameter('developer', 'form', 'error_messages', 'url')) {
            $this->addParameter($groupId, array('name' => 'url', 'translation' => 'Url', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a valid URL', 'comment' => ''));
        }
        if(!$this->getParameter('developer', 'form', 'error_messages', 'max')) {
            $this->addParameter($groupId, array('name' => 'max', 'translation' => 'Max', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a value no larger than $1', 'comment' => ''));
        }
        if(!$this->getParameter('developer', 'form', 'error_messages', 'min')) {
            $this->addParameter($groupId, array('name' => 'min', 'translation' => 'Min', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a value of at least $1', 'comment' => ''));
        }
        if(!$this->getParameter('developer', 'form', 'error_messages', 'required')) {
            $this->addParameter($groupId, array('name' => 'required', 'translation' => 'Required', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please complete this mandatory field', 'comment' => ''));
        }


        $parametersGroup = $this->getParameterGroup($moduleId, 'admin_translations');
        if ($parametersGroup) {
            $groupId = $parametersGroup['id'];
        } else {
            $groupId = $this->addParameterGroup($moduleId, 'admin_translations', 'Admin translations', 1);
        }

        if(!$this->getParameter('developer', 'form', 'admin_translations', 'type_text')) {
            $this->addStringParameter($groupId, 'Type text', 'type_text', 'Text', 0);
        }
        if(!$this->getParameter('developer', 'form', 'admin_translations', 'type_captcha')) {
            $this->addStringParameter($groupId, 'Type captcha', 'type_captcha', 'Captcha', 0);
        }
        if(!$this->getParameter('developer', 'form', 'admin_translations', 'type_confirm')) {
            $this->addStringParameter($groupId, 'Type confirm', 'type_confirm', 'Confirm', 0);
        }
        if(!$this->getParameter('developer', 'form', 'admin_translations', 'type_email')) {
            $this->addStringParameter($groupId, 'Type email', 'type_email', 'Email', 0);
        }
        if(!$this->getParameter('developer', 'form', 'admin_translations', 'type_radio')) {
            $this->addStringParameter($groupId, 'Type radiopublic static', 'type_radio', 'Radio', 0);
        }
        if(!$this->getParameter('developer', 'form', 'admin_translations', 'type_select')) {
            $this->addStringParameter($groupId, 'Type select', 'type_select', 'Select', 0);
        }
        if(!$this->getParameter('developer', 'form', 'admin_translations', 'type_textarea')) {
            $this->addStringParameter($groupId, 'Type textarea', 'type_textarea', 'Textarea', 0);
        }



        //bind widget images to repository
        $sql = "SELECT * FROM ".$this->dbPref."m_content_management_widget WHERE 1";
        $rs = mysql_query($sql, $this->conn);
        if (!$rs) {
            throw new \Exception($sql . " " . mysql_error());
        }
        while($lock = mysql_fetch_assoc($rs)){
            $this->bindToRepository($lock);
        }

    }

    public function getNotes($cf)
    {
        $note = 
 '
<P><span style="color: red;">ATTENTION</span></P>
<p>You are updating from 2.0 or older.
IpForm widget has been introduced since then.
You need manually replace your current ip_content.css and 960.css files
 (ip_themes/lt_pagan/) to ones from downloaded archive.
 If you have made some changes to original files, please replicate those changes on new files.
</p>
<p>If you are using other theme, you need manually tweak your CSS
to style forms.</p>
    ';
        $notes = array($note);
        return $notes;
    }
    
    
    /**
     * (non-PHPdoc)
    * @see IpUpdate\Library\Migration.General::getSourceVersion()
    */
    public function getSourceVersion()
    {
        return '2.0';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '2.1';
    }


    private function bindToRepository($widgetRecord) {

        $data = json_decode($widgetRecord['data'], true);
        if (empty($data)) {
            return; //don't need to do anything
        }
        $id = $widgetRecord['widgetId'];
        switch($widgetRecord['name']) {
            case 'IpImage':
            case 'IpTextImage':
                if (isset($data['imageOriginal']) && $data['imageOriginal']) {
                    if (!$this->isBind($data['imageOriginal'], 'standard/content_management', $id)) {
                        $this->bindFile($data['imageOriginal'], 'standard/content_management', $id);
                    }
                }
                if (isset($data['imageBig']) && $data['imageBig']) {
                    if (!$this->isBind($data['imageBig'], 'standard/content_management', $id)) {
                        $this->bindFile($data['imageBig'], 'standard/content_management', $id);
                    }
                }
                if (isset($data['imageSmall']) && $data['imageSmall']) {
                    if (!$this->isBind($data['imageSmall'], 'standard/content_management', $id)) {
                        $this->bindFile($data['imageSmall'], 'standard/content_management', $id);
                    }
                }
                break;
            case 'IpImageGallery':
                if (!isset($data['images']) || !is_array($data['images'])) {
                    break;
                }
                foreach($data['images'] as $imageKey => $image) {
                    if (!is_array($image)) {
                        break;
                    }
                    if (isset($image['imageOriginal']) && $image['imageOriginal']) {
                        if (!$this->isBind($image['imageOriginal'], 'standard/content_management', $id)) {
                            $this->bindFile($image['imageOriginal'], 'standard/content_management', $id);
                        }
                    }
                    if (isset($image['imageBig']) && $image['imageBig']) {
                        if (!$this->isBind($image['imageBig'], 'standard/content_management', $id)) {
                            $this->bindFile($image['imageBig'], 'standard/content_management', $id);
                        }
                    }
                    if (isset($image['imageSmall']) && $image['imageSmall']) {
                        if (!$this->isBind($image['imageSmall'], 'standard/content_management', $id)) {
                            $this->bindFile($image['imageSmall'], 'standard/content_management', $id);
                        }
                    }
                }

                break;
            case 'IpLogoGallery':
                if (!isset($data['logos']) || !is_array($data['logos'])) {
                    break;
                }

                foreach($data['logos'] as $logoKey => $logo) {
                    if (!is_array($logo)) {
                        break;
                    }
                    if (isset($logo['logoOriginal']) && $logo['logoOriginal']) {
                        if (!$this->isBind($logo['logoOriginal'], 'standard/content_management', $id)) {
                            $this->bindFile($logo['logoOriginal'], 'standard/content_management', $id);
                        }
                    }
                    if (isset($logo['logoSmall']) && $logo['logoSmall']) {
                        if (!$this->isBind($logo['logoSmall'], 'standard/content_management', $id)) {
                            $this->bindFile($logo['logoSmall'], 'standard/content_management', $id);
                        }
                    }
                };
                break;
            case 'IpFile':
                if (!isset($data['files']) || !is_array($data['files'])) {
                    return;
                }
                foreach($data['files'] as $fileKey => $file) {
                    if (isset($file['fileName']) && $file['fileName']) {
                        if (!$this->isBind($file['fileName'], 'standard/content_management', $id)) {
                            $this->bindFile($file['fileName'], 'standard/content_management', $id);
                        }
                    }
                };
                break;
            default:
                //don't do anything with other widgets
        }
    }

    private function isBind($file, $module, $instanceId) {
        $sql = "
        SELECT
        *
        FROM
        `".$this->dbPref."m_administrator_repository_file`
        WHERE
        `fileName` = '".mysql_real_escape_string($file)."' AND
        `module` = '".mysql_real_escape_string($module)."' AND
        `instanceId` = '".mysql_real_escape_string($instanceId)."'
        ";

        $rs = mysql_query($sql, $this->conn);
        if (!$rs){
            throw new Exception('Can\'t bind new instance to the file '.$sql.' '.mysql_error(), Exception::DB);
        }

        if($lock = mysql_fetch_assoc($rs)) {
            return $lock['date'];
        } else {
            return false;
        }

    }
    
    private function bindFile($file, $module, $instanceId) {
        $sql = "
        INSERT INTO
        `".$this->dbPref."m_administrator_repository_file`
        SET
        `fileName` = '".mysql_real_escape_string($file)."',
        `module` = '".mysql_real_escape_string($module)."',
        `instanceId` = '".mysql_real_escape_string($instanceId)."',
        `date` = '".time()."'
        ";

        $rs = mysql_query($sql, $this->conn);
        if (!$rs){
            throw new Exception('Can\'t bind new instance to the file '.$sql.' '.mysql_error(), Exception::DB);
        }

    }
    
    private function deleteParameter($moduleGroup, $module, $parameterGroup, $parameterName){
        $parameter = $this->getParameter($moduleGroup, $module, $parameterGroup, $parameterName);

        if($parameter){
            $sql = false;
            switch($parameter['type']){
                case 'string_wysiwyg':
                case 'string':
                case 'textarea':
                    $sql = "delete from `".$this->dbPref."par_string` where parameter_id = ".(int)$parameter['id']."";
                    break;
                case 'integer':
                    $sql = "delete from `".$this->dbPref."par_integer` where parameter_id = ".(int)$parameter['id']."";
                    break;
                case 'bool':
                    $sql = "delete from `".$this->dbPref."par_bool` where parameter_id = ".(int)$parameter['id']."";
                    break;
                case 'lang':
                case 'lang_textarea':
                case 'lang_wysiwyg':
                    $sql = "delete from `".$this->dbPref."par_lang` where parameter_id = ".(int)$parameter['id']."";
                    break;
            }

            if($sql){
                $rs = mysql_query($sql, $this->conn);
                if(!$rs)
                    throw new \IpUpdate\Library\UpdateException($sql.' '.mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                $sql = "delete from `".$this->dbPref."parameter` where id = ".(int)$parameter['id']."";
                $rs = mysql_query($sql, $this->conn);
                if(!$rs)
                    throw new \IpUpdate\Library\UpdateException($sql.' '.mysql_error(), \IpUpdate\Library\UpdateException::SQL);

            }
        }
    }

    private function getModuleId($group_name, $module_name){
        $answer = array();
        $sql = "select m.id from `".$this->dbPref."module` m, `".$this->dbPref."module_group` g
        where m.`group_id` = g.`id` and g.`name` = '".mysql_real_escape_string($group_name)."' and m.`name` = '".mysql_real_escape_string($module_name)."' ";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock['id'];
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }

    }

    private function getParametersGroup($moduleId, $name)
    {
        $sql = "select * from `".$this->dbPref."parameter_group` where `module_id` = '".mysql_real_escape_string($moduleId)."' and `name` = '".mysql_real_escape_string($name)."' ";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock;
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }

    private function getParameter($moduleGroupName, $moduleName, $parameterGroupName, $parameterName)
    {
        $sql = "select * from `".$this->dbPref."module_group` mg, `".$this->dbPref."module` m, `".$this->dbPref."parameter_group` pg, `".$this->dbPref."parameter` p
        where p.group_id = pg.id and pg.module_id = m.id and m.group_id = mg.id
        and mg.name = '".mysql_real_escape_string($moduleGroupName)."' and m.name = '".mysql_real_escape_string($moduleName)."' and pg.name = '".mysql_real_escape_string($parameterGroupName)."' and p.name = '".mysql_real_escape_string($parameterName)."'";
        $rs = mysql_query($sql, $this->conn);
        if ($rs) {
            if($lock = mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }

    }

    private function addParameter($groupId, $parameter) {
        $sql = "insert into `".$this->dbPref."parameter`
        set `name` = '".mysql_real_escape_string($parameter['name'])."',
        `admin` = '".mysql_real_escape_string($parameter['admin'])."',
        `group_id` = ".(int)$groupId.",
        `translation` = '".mysql_real_escape_string($parameter['translation'])."',
        `comment` = '".mysql_real_escape_string($parameter['comment'])."',
        `type` = '".mysql_real_escape_string($parameter['type'])."'";

        $rs = mysql_query($sql, $this->conn);
        if($rs) {
            $last_insert_id = mysql_insert_id();
            switch($parameter['type']) {
                case "string_wysiwyg":
                    $sql = "insert into `".$this->dbPref."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql, $this->conn);
                    if(!$rs)
                        throw new \IpUpdate\Library\UpdateException("Can't insert parameter ".$sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    break;
                case "string":
                    $sql = "insert into `".$this->dbPref."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql, $this->conn);
                    if(!$rs)
                        throw new \IpUpdate\Library\UpdateException("Can't insert parameter ".$sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    break;
                case "integer":
                    $sql = "insert into `".$this->dbPref."par_integer` set `value` = ".mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql, $this->conn);
                    if(!$rs)
                        throw new \IpUpdate\Library\UpdateException("Can't insert parameter ".$sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    break;
                case "bool":
                    $sql = "insert into `".$this->dbPref."par_bool` set `value` = ".mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql, $this->conn);
                    if(!$rs)
                        throw new \IpUpdate\Library\UpdateException("Can't insert parameter ".$sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    break;
                case "textarea":
                    $sql = "insert into `".$this->dbPref."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                    $rs = mysql_query($sql, $this->conn);
                    if(!$rs)
                        throw new \IpUpdate\Library\UpdateException("Can't insert parameter ".$sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    break;

                case "lang":
                    $languages = self::getLanguages();
                    foreach($languages as $key => $language) {
                        $sql3 = "insert into `".$this->dbPref."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = mysql_query($sql3, $this->conn);
                        if(!$rs3)
                            throw new \IpUpdate\Library\UpdateException("Can't update parameter ".$sql3." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    }
                    break;
                case "lang_textarea":
                    $languages = self::getLanguages();
                    foreach($languages as $key => $language) {
                        $sql3 = "insert into `".$this->dbPref."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = mysql_query($sql3, $this->conn);
                        if(!$rs3)
                            throw new \IpUpdate\Library\UpdateException("Can't update parameter ".$sql3." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    }
                    break;
                case "lang_wysiwyg":
                    $languages = self::getLanguages();
                    foreach($languages as $key => $language) {
                        $sql3 = "insert into `".$this->dbPref."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                        $rs3 = mysql_query($sql3, $this->conn);
                        if(!$rs3)
                            throw new \IpUpdate\Library\UpdateException("Can't update parameter ".$sql3." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    }
                    break;
            }
        }else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
        }
    }

    private function getModuleGroup($name){
        $sql = "select * from `".$this->dbPref."module_group` where name = '".mysql_real_escape_string($name)."' ";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock;
            } else {
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    

    private function addModule($groupId, $moduleTranslation, $moduleName, $admin, $managed, $core, $version, $rowNumber = 0){
        $sql = "insert into `".$this->dbPref."module`
        set
        group_id = '".(int)$groupId."',
        name = '".mysql_real_escape_string($moduleName)."',
        translation = '".mysql_real_escape_string($moduleTranslation)."',
        admin = '".(int)$admin."',
        managed = '".(int)$managed."',
        core = '".(int)$core."',
        row_number = '".(int)$rowNumber."',
        version = '".mysql_real_escape_string($version)."'

        ";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            return mysql_insert_id();
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    
    
    private function getUsers(){
        $answer = array();
        $sql = "select * from `".$this->dbPref."user` where 1";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                $answer[] = $lock;
            }
            return $answer;
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }

    }
    
    private function addPermissions($moduleId, $userId){
        $sql = "insert into `".$this->dbPref."user_to_mod`
        set
        module_id = '".(int)$moduleId."',
        user_id = '".(int)$userId."'

        ";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            return mysql_insert_id();
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    
    private function getParameterGroup($module_id, $group_name){
        $sql = "select * from `".$this->dbPref."parameter_group` where `module_id` = '".(int)$module_id."' and `name` = '".mysql_real_escape_string($group_name)."'";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            if($lock = mysql_fetch_assoc($rs))
                return $lock;
            else
                return false;
        }else{
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    
    private function addParameterGroup($moduleId, $name, $translation, $admin){
        $sql = "insert into `".$this->dbPref."parameter_group`
        set
        `name` = '".mysql_real_escape_string($name)."',
        `translation` = '".mysql_real_escape_string($translation)."',
        `module_id` = '".(int)$moduleId."',
        `admin` = '".(int)$admin."'
        ";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            return mysql_insert_id();
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    
    private function getSystemVariable($name){
        $sql = "select value from `".$this->dbPref."variables`  where `name` = '".mysql_real_escape_string($name)."'";
        $rs = mysql_query($sql, $this->conn);
        if ($rs) {
            if ($lock = mysql_fetch_assoc($rs)) {
                return $lock['value'];
            } else
                return false;
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    
    private function insertSystemVariable($name, $value){
        $sql = "insert into `".$this->dbPref."variables` set `value` = '".mysql_real_escape_string($value)."', `name` = '".mysql_real_escape_string($name)."'";
        $rs = mysql_query($sql, $this->conn);
        if (!$rs) {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }
    
    private function getModule($id=null, $groupName=null , $moduleName = null)
    {
        if($id != null)
            $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".$this->dbPref."module_group` g, `".$this->dbPref."module` m where m.id = '".mysql_real_escape_string($id)."' and  m.group_id = g.id order by g.row_number, m.row_number limit 1";
        elseif($groupName != null && $moduleName != null)
        $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".$this->dbPref."module_group` g, `".$this->dbPref."module` m where g.name = '".mysql_real_escape_string($groupName)."' and m.group_id = g.id and m.name= '".mysql_real_escape_string($moduleName)."' order by g.row_number, m.row_number limit 1";
        else
            $sql = "select m.core, m.id, g.name as g_name, m.name as m_name from `".$this->dbPref."module_group` g, `".$this->dbPref."module` m where m.group_id = g.id order by g.row_number, m.row_number limit 1";
        $rs = mysql_query($sql, $this->conn);
        if($rs)
        {
            if($lock = mysql_fetch_assoc($rs))
                return $lock;
            else
                return false;
        }else
        {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }

    private function addStringParameter($groupId, $translation, $name, $value, $admin)
    {
        $sql = "INSERT INTO `".$this->dbPref."parameter` (`name`, `admin`, `regexpression`, `group_id`, `translation`, `comment`, `type`)
        VALUES ('".mysql_real_escape_string($name)."', ".(int)$admin.", '', ".(int)$groupId.", '".mysql_real_escape_string($translation)."', NULL, 'string')";
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            $sql2 = "INSERT INTO `".$this->dbPref."par_string` (`value`, `parameter_id`)
            VALUES ('".mysql_real_escape_string($value)."', ".mysql_insert_id().");";
            $rs2 = mysql_query($sql2, $this->conn);
            if($rs2) {
                return true;
            } else {
                throw new \IpUpdate\Library\UpdateException($sql2." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                return false;
            }
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }

    }
    
    private function getLanguages(){
        $answer = array();
        $sql = "select * from `".$this->dbPref."language` where 1 order by row_number";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs))
                $answer[] = $lock;
        }else{
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
        }
        return $answer;
    }
    
}
