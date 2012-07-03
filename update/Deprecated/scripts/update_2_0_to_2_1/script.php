<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_2_0_to_2_1;

if (!defined('CMS')) exit;

require_once('translations.php');
require_once('repository.php');

class Script {
    var $deleteFiles;
    var $addFiles;
    var $deleteFolders;
    var $addFolders;

    var $stepCount;
    var $curStep;
    var $curAction;


    public function __construct($stepCount, $curStep, $curAction) {
        $this->deleteFolders = array();
        $this->deleteFolders[] = 'install';
        $this->deleteFolders[] = 'ip_cms';
        $this->deleteFolders[] = 'ip_libs';


        $this->deleteFiles = array();
        $this->deleteFiles[] = 'admin.php';
        $this->deleteFiles[] = 'index.php';
        $this->deleteFiles[] = 'ip_backend_frames.php';
        $this->deleteFiles[] = 'ip_backend_worker.php';
        $this->deleteFiles[] = 'ip_cron.php';
        $this->deleteFiles[] = 'ip_license.html';
        $this->deleteFiles[] = 'sitemap.php';

        $this->addFolders = array();
        $this->addFolders[] = 'ip_cms';
        $this->addFolders[] = 'ip_libs';
        $this->addFolders[] = 'ip_plugins';

        $this->addFiles = array();
        $this->addFiles[] = 'admin.php';
        $this->addFiles[] = 'index.php';
        $this->addFiles[] = 'ip_backend_frames.php';
        $this->addFiles[] = 'ip_backend_worker.php';
        $this->addFiles[] = 'ip_cron.php';
        $this->addFiles[] = 'ip_license.html';
        $this->addFiles[] = 'sitemap.php';

        $this->stepCount = $stepCount;
        $this->curStep = $curStep;
        $this->curAction = $curAction;
    }

    public function getActionsCount() {
        return 3;
    }

    public function process () {
        global $htmlOutput;
        global $navigation;

        $answer = '';


        switch ($this->curAction) {
            default:
            case 1:
                $answer .= $this->filesToDelete();
                break;
            case 2:
                $answer .= $this->filesToUpload();
                break;
            case 3:
                $answer .= $this->updateDatabase();
                break;
        }


        return $answer;
    }



    public function needToDelete() {
        $answer = false;
        if($this->curStep == 1 && !isset($_SESSION['process'][1]['deleted'])) {
            foreach ($this->deleteFolders as $folder){
                if (is_dir('../'.$folder) ) {
                    $answer = true;
                }
            }
            foreach ($this->deleteFiles as $file){
                if (is_file('../'.$file) ) {
                    $answer = true;
                }
            }

            if ($answer == false) {
                $_SESSION['process'][1]['deleted'] = true;
            }
        }
        return $answer;
    }

    public function needToUpload() {
        $answer = false;
        if($this->curStep == $this->stepCount && !isset($_SESSION['process'][1]['uploaded'])) {
            foreach ($this->addFolders as $folder){
                if (!is_dir('../'.$folder) ) {
                    $answer = true;
                }
            }
            foreach ($this->addFiles as $file){
                if (!is_file('../'.$file) ) {
                    $answer = true;
                }
            }

            if ($answer == false) {
                $_SESSION['process'][1]['uploaded'] = true;
            }
        }
        return $answer;
    }

    public function filesToDelete() {
        global $navigation;
        global $htmlOutput;

        $answer = '';

        $tableFolders = array();

        foreach ($this->deleteFolders as $folder){
            if (is_dir('../'.$folder) ) {
                $tableFolders[] = '/'.$folder.'/';
                $tableFolders[] = '';
            }
        }


        if (sizeof($tableFolders)) {
            $answer .= REMOVE_DIRECTORIES.$htmlOutput->table($tableFolders);
            $answer .= '<br/>';
        }



        $tableFiles = array();
        foreach ($this->deleteFiles as $file){
            if (is_file('../'.$file) ) {
                $tableFiles[] = '/'.$file;
                $tableFiles[] = '';
            }
        }

        if (sizeof($tableFiles)) {
            $answer .= REMOVE_FILES.$htmlOutput->table($tableFiles);
        }

        if ($this->needToDelete())
        $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
        else {
            header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
        }

        return $answer;
    }

    public function filesToUpload(){
        global $navigation;
        global $htmlOutput;

        $answer = '';

        $tableFolders = array();

        foreach ($this->addFolders as $folder){
            if (!is_dir('../'.$folder) ) {
                $tableFolders[] = '/'.$folder.'/';
                $tableFolders[] = '';
            }
        }


        if (sizeof($tableFolders)) {
            $answer .= UPLOAD_DIRECTORIES.$htmlOutput->table($tableFolders);
            $answer .= '<br/>';
        }



        $tableFiles = array();
        foreach ($this->addFiles as $file){
            if (!is_file('../'.$file) ) {
                $tableFiles[] = '/'.$file;
                $tableFiles[] = '';
            }
        }

        if (sizeof($tableFiles)) {
            $answer .= UPLOAD_FILES.$htmlOutput->table($tableFiles);
        }

        if ($this->needToUpload())
        $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
        else {
            header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
        }
        return $answer;
    }





    public function updateDatabase() {
        global $navigation;
        global $htmlOutput;
        
        $answer = '';
        if (\Db_100::getSystemVariable('version') != '2.1') {
            
            
            $parametersRefractor = new \ParametersRefractor();
            
            $parametersRefractor->deleteParameter('standard', 'content_management', 'widget_faq', 'title');
            $parametersRefractor->deleteParameter('standard', 'content_management', 'widget_faq', 'text');
            
            $module = \Db_100::getModule(null, 'standard', 'content_management');
            
            $group = $parametersRefractor->getParametersGroup($module['id'], 'widget_faq');
            if ($group) {
                if(!\Db_100::getParameter('standard', 'content_management', 'widget_faq', 'question')) {
                    \Db_100::addStringParameter($group['id'], 'Question', 'question', 'Question', 1);
                }
                if(!\Db_100::getParameter('standard', 'content_management', 'admin_translations', 'answer')) {
                    \Db_100::addStringParameter($group['id'], 'Answer', 'answer', 'Answer', 1);
                }
            }

            $group = $parametersRefractor->getParametersGroup($module['id'], 'widget_contact_form');
            if ($group) {
                if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'move')) {
                    \Db_100::addStringParameter($group['id'], 'Move', 'move', 'Move', 1);
                }
                if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'remove')) {
                    \Db_100::addStringParameter($group['id'], 'Remove', 'remove', 'Remove', 1);
                }

                if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'options')) {
                    \Db_100::addStringParameter($group['id'], 'Options', 'options', 'Options', 1);
                }
                
                if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'send')) {
                    \Db_100::addParameter($group['id'], array('name' => 'send', 'translation' => 'Send', 'admin' => 0, 'type'=> 'lang', 'value' => 'Send', 'comment' => ''));
                }
                
            }
            
            $module = \Db_100::getModule(null, 'standard', 'configuration');
            $group = $parametersRefractor->getParametersGroup($module['id'], 'main_parameters');
            if ($group) {
                if(!\Db_100::getParameter('standard', 'configuration', 'main_parameters', 'email_title')) {
                    \Db_100::addParameter($group['id'], array('name' => 'email_title', 'translation' => 'Default email title', 'admin' => 0, 'type'=> 'lang', 'value' => 'Hi,', 'comment' => ''));
                }
                
            }
            
            $module = \Db_100::getModule(null, 'community', 'user');
            $group = $parametersRefractor->getParametersGroup($module['id'], 'admin_translations');
            if ($group) {
                if(!\Db_100::getParameter('community', 'user', 'admin_translations', 'registration')) {
                    \Db_100::addStringParameter($group['id'], 'Registration', 'registration', 'Registration', 1);
                }
                
                
            }
            

            $group = $parametersRefractor->getParametersGroup($module['id'], 'translations');
            if ($group) {
                if(!\Db_100::getParameter('community', 'user', 'translations', 'text_registration_verified')) {
                    \Db_100::addParameter($group['id'], array('name' => 'text_registration_verified', 'translation' => 'Text - registration verified', 'admin' => 0, 'type'=> 'lang_wysiwyg', 'value' => 'Registration has been aproved. You can login now.', 'comment' => ''));
                }
            }
            
            $moduleGroup = $parametersRefractor->getModuleGroup('administrator');
            $moduleId = $parametersRefractor->getModuleId('administrator', 'theme'); 
            if ($moduleId === false) {
                $moduleId = $parametersRefractor->addModule($moduleGroup['id'], 'Theme', 'theme', true, true, true, '1.00');
                $users = $parametersRefractor->getUsers();
                foreach($users as $user){
                    $parametersRefractor->addPermissions($moduleId, $user['id']);
                }
            }
            $parametersGroup = \Db_100::getParameterGroup($moduleId, 'admin_translations');
            if ($parametersGroup) {
                $groupId = $parametersGroup['id'];
            } else {
                $groupId = $parametersRefractor->addParameterGroup($moduleId, 'admin_translations', 'Admin translations', 1);
            }
            \Db_100::addStringParameter($groupId, 'Successful install', 'successful_install', 'New theme has been successfully installed.', 1);
            \Db_100::addStringParameter($groupId, 'Install', 'install', 'Install', 1);
            \Db_100::addStringParameter($groupId, 'Title', 'title', 'Choose theme', 1);
            
            $sql = "ALTER TABLE `".DB_PREF."m_administrator_repository_file` ADD INDEX (  `filename` )";
            $rs = mysql_query($sql);
            if (!$rs) {
                trigger_error($sql.' '.mysql_error());
            }
            
            $rs = mysql_query("SHOW COLUMNS FROM `".DB_PREF."m_content_management_widget` LIKE 'recreated'");
            $columnExists = (mysql_num_rows($rs)) ? true : false;
            if (!$columnExists) {
                $sql = "ALTER TABLE `".DB_PREF."m_content_management_widget` ADD  `recreated` INT NOT NULL COMMENT  'when last time the images were cropped freshly' AFTER `created`";
                $rs = mysql_query($sql);
                if (!$rs) {
                    trigger_error($sql.' '.mysql_error());
                }
            }
            
            $sql = "UPDATE `".DB_PREF."m_content_management_widget` SET recreated = created WHERE 1";
            $rs = mysql_query($sql);
            if (!$rs) {
                trigger_error($sql.' '.mysql_error());
            }
            
            if (\Db_100::getSystemVariable('theme_changed') === false) {
                \Db_100::insertSystemVariable('theme_changed', time());
            }
            
            
            if (\Db_100::getSystemVariable('last_system_message_sent') === false) {
                \Db_100::insertSystemVariable('last_system_message_sent', '');
            }
            
            if (\Db_100::getSystemVariable('last_system_message_shown') === false) {
                \Db_100::insertSystemVariable('last_system_message_shown', '');
            }
            
            
            //add developer/form module
            $moduleGroup = $parametersRefractor->getModuleGroup('developer');
            $moduleId = $parametersRefractor->getModuleId('developer', 'form'); 
            if ($moduleId === false) {
                $moduleId = $parametersRefractor->addModule($moduleGroup['id'], 'Form', 'form', false, false, true, '1.00');
                $users = $parametersRefractor->getUsers();
                foreach($users as $user){
                    $parametersRefractor->addPermissions($moduleId, $user['id']);
                }
            }
            $parametersGroup = \Db_100::getParameterGroup($moduleId, 'error_messages');
            if ($parametersGroup) {
                $groupId = $parametersGroup['id'];
            } else {
                $groupId = $parametersRefractor->addParameterGroup($moduleId, 'error_messages', 'Error messages', 0);
            }
            
            if(!\Db_100::getParameter('developer', 'form', 'error_messages', 'unknown')) {
                \Db_100::addParameter($groupId, array('name' => 'unknown', 'translation' => 'Unknown', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please correct this value', 'comment' => ''));
            }
            if(!\Db_100::getParameter('developer', 'form', 'error_messages', 'email')) {
                \Db_100::addParameter($groupId, array('name' => 'email', 'translation' => 'Email', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a valid email address', 'comment' => ''));
            }
            if(!\Db_100::getParameter('developer', 'form', 'error_messages', 'number')) {
                \Db_100::addParameter($groupId, array('name' => 'number', 'translation' => 'Number', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a valid numeric value', 'comment' => ''));
            }
            if(!\Db_100::getParameter('developer', 'form', 'error_messages', 'url')) {
                \Db_100::addParameter($groupId, array('name' => 'url', 'translation' => 'Url', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a valid URL', 'comment' => ''));
            }
            if(!\Db_100::getParameter('developer', 'form', 'error_messages', 'max')) {
                \Db_100::addParameter($groupId, array('name' => 'max', 'translation' => 'Max', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a value no larger than $1', 'comment' => ''));
            }
            if(!\Db_100::getParameter('developer', 'form', 'error_messages', 'min')) {
                \Db_100::addParameter($groupId, array('name' => 'min', 'translation' => 'Min', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please enter a value of at least $1', 'comment' => ''));
            }
            if(!\Db_100::getParameter('developer', 'form', 'error_messages', 'required')) {
                \Db_100::addParameter($groupId, array('name' => 'required', 'translation' => 'Required', 'admin' => 0, 'type'=> 'lang', 'value' => 'Please complete this mandatory field', 'comment' => ''));
            }
            
            
            $parametersGroup = \Db_100::getParameterGroup($moduleId, 'admin_translations');
            if ($parametersGroup) {
                $groupId = $parametersGroup['id'];
            } else {
                $groupId = $parametersRefractor->addParameterGroup($moduleId, 'admin_translations', 'Admin translations', 1);
            }
            
            if(!\Db_100::getParameter('developer', 'form', 'admin_translations', 'type_text')) {
                \Db_100::addStringParameter($groupId, 'Type text', 'type_text', 'Text', 0);
            }
            if(!\Db_100::getParameter('developer', 'form', 'admin_translations', 'type_captcha')) {
                \Db_100::addStringParameter($groupId, 'Type captcha', 'type_captcha', 'Captcha', 0);
            }
            if(!\Db_100::getParameter('developer', 'form', 'admin_translations', 'type_confirm')) {
                \Db_100::addStringParameter($groupId, 'Type confirm', 'type_confirm', 'Confirm', 0);
            }
            if(!\Db_100::getParameter('developer', 'form', 'admin_translations', 'type_email')) {
                \Db_100::addStringParameter($groupId, 'Type email', 'type_email', 'Email', 0);
            }
            if(!\Db_100::getParameter('developer', 'form', 'admin_translations', 'type_radio')) {
                \Db_100::addStringParameter($groupId, 'Type radio', 'type_radio', 'Radio', 0);
            }
            if(!\Db_100::getParameter('developer', 'form', 'admin_translations', 'type_select')) {
                \Db_100::addStringParameter($groupId, 'Type select', 'type_select', 'Select', 0);
            }
            if(!\Db_100::getParameter('developer', 'form', 'admin_translations', 'type_textarea')) {
                \Db_100::addStringParameter($groupId, 'Type textarea', 'type_textarea', 'Textarea', 0);
            }
            
            
            
            //bind widget images to repository
            $sql = "SELECT * FROM ".DB_PREF."m_content_management_widget WHERE 1";
            $rs = mysql_query($sql);
            if (!$rs) {
                throw new \Exception($sql . " " . mysql_error());
            }
            while($lock = mysql_fetch_assoc($rs)){
                $this->bindToRepository($lock);
            }
            
            if ($this->curStep == $this->stepCount){
                \Db_100::setSystemVariable('version','2.1');
            }
        }

        if ($this->curStep == $this->stepCount) {
            header("location: ".$navigation->generateLink($navigation->curStep() + 1));
        } else {
            header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript() + 1));
        }

        return $answer;
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
                    if (!\Modules\administrator\repository\Model::isBind($data['imageOriginal'], 'standard/content_management', $id)) {
                        \Modules\administrator\repository\Model::bindFile($data['imageOriginal'], 'standard/content_management', $id);
                    }
                }
                if (isset($data['imageBig']) && $data['imageBig']) {
                    if (!\Modules\administrator\repository\Model::isBind($data['imageBig'], 'standard/content_management', $id)) {
                        \Modules\administrator\repository\Model::bindFile($data['imageBig'], 'standard/content_management', $id);
                    }
                }
                if (isset($data['imageSmall']) && $data['imageSmall']) {
                    if (!\Modules\administrator\repository\Model::isBind($data['imageSmall'], 'standard/content_management', $id)) {
                        \Modules\administrator\repository\Model::bindFile($data['imageSmall'], 'standard/content_management', $id);
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
                        if (!\Modules\administrator\repository\Model::isBind($image['imageOriginal'], 'standard/content_management', $id)) {
                            \Modules\administrator\repository\Model::bindFile($image['imageOriginal'], 'standard/content_management', $id);
                        }
                    }
                    if (isset($image['imageBig']) && $image['imageBig']) {
                        if (!\Modules\administrator\repository\Model::isBind($image['imageBig'], 'standard/content_management', $id)) {
                            \Modules\administrator\repository\Model::bindFile($image['imageBig'], 'standard/content_management', $id);
                        }
                    }
                    if (isset($image['imageSmall']) && $image['imageSmall']) {
                        if (!\Modules\administrator\repository\Model::isBind($image['imageSmall'], 'standard/content_management', $id)) {
                            \Modules\administrator\repository\Model::bindFile($image['imageSmall'], 'standard/content_management', $id);
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
                        if (!\Modules\administrator\repository\Model::isBind($logo['logoOriginal'], 'standard/content_management', $id)) {
                            \Modules\administrator\repository\Model::bindFile($logo['logoOriginal'], 'standard/content_management', $id);
                        }
                    }
                    if (isset($logo['logoSmall']) && $logo['logoSmall']) {
                        if (!\Modules\administrator\repository\Model::isBind($logo['logoSmall'], 'standard/content_management', $id)) {
                            \Modules\administrator\repository\Model::bindFile($logo['logoSmall'], 'standard/content_management', $id);
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
                        if (!\Modules\administrator\repository\Model::isBind($file['fileName'], 'standard/content_management', $id)) {
                            \Modules\administrator\repository\Model::bindFile($file['fileName'], 'standard/content_management', $id);
                        }
                    }
                };
                break;
            default:
                //don't do anything with other widgets
        }
    }






}

