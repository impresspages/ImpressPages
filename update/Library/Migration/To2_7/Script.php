<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

namespace IpUpdate\Library\Migration\To2_7;


class Script extends \IpUpdate\Library\Migration\General{

    private $conn;
    private $dbh;
    private $dbPref;

    public function process($cf)
    {
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);
        $this->conn = $conn;
        $dbh = $db->connect($cf);
        $this->dbh = $dbh;

        $this->dbPref = $cf['DB_PREF'];

        $this->addReflectionTable();


        $this->createNewDirs($cf);
        $this->removeDeletedDir($cf);


        $this->importParameters('newParameters.php');

        $this->migrateWidgets($cf);

    }

    public function removeDeletedDir($cf)
    {
        //move deleted dir to secure folder
        $fileSystem = new \IpUpdate\Library\Helper\FileSystem();
        $fileSystem->rm($cf['BASE_DIR'].$cf['FILE_DIR'].'deleted/');
    }

    public function migrateWidgets($cf)
    {
        $this->dbPref = $cf['DB_PREF'];
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf);

        $widgetsToMigrate = array(
            'IpImage',
            'IpImageGallery',
            'IpLogoGallery',
            'IpTextImage'
        );

        foreach($widgetsToMigrate as $widgetName) {
            $sql = "
            SELECT
                `widgetId`, `data`
            FROM
                `".$this->dbPref."m_content_management_widget`
            where
                name = :name
            ";
            $params = array(
                'name' => $widgetName
            );
            $q = $conn->prepare($sql);
            try {
                $q->execute($params);
            } catch (\PDOException $e) {
                echo 'exception '.$e->getMessage();
            }
            $widgetsData = $q->fetchAll();
            $migrateFunction = 'migrate'.$widgetName;
            foreach($widgetsData as $widgetData) {

                $newData = $this->$migrateFunction($widgetData['widgetId'], json_decode($widgetData['data'], true));
                $sql = "
                    UPDATE
                        `".$this->dbPref."m_content_management_widget`
                    SET
                        `data` = :data
                    WHERE
                        `widgetId` = :widgetId
                ";

                $params = array (
                    'data' => json_encode($this->checkEncoding($newData)),
                    'widgetId' => $widgetData['widgetId']
                );
                $q = $conn->prepare($sql);
                $q->execute($params);
            }



        }
    }



    private function migrateIpImage($widgetId, $data)
    {
        $repository = \Modules\administrator\repository\Model::instance();
        if (isset($data['imageBig']) && $data['imageBig']) {
            $repository->unbindFile($data['imageBig'], 'standard/content_management', $widgetId);
            unset($data['imageBig']);
        }
        if (isset($data['imageSmall']) && $data['imageSmall']) {
            $repository->unbindFile($data['imageSmall'], 'standard/content_management', $widgetId);
            unset($data['imageSmall']);
        }
        return $data;
    }

    private function migrateIpImageGallery($widgetId, $data)
    {
        if (isset($data['images']) && is_array($data['images'])) {
            foreach($data['images'] as $imageKey => &$image) {
                if (!is_array($image)) {
                    continue;
                }
                if (isset($image['imageBig']) && $image['imageBig']) {
                    \Modules\administrator\repository\Model::unbindFile($image['imageBig'], 'standard/content_management', $widgetId);
                    unset($image['imageBig']);
                }
                if (isset($image['imageSmall']) && $image['imageSmall']) {
                    \Modules\administrator\repository\Model::unbindFile($image['imageSmall'], 'standard/content_management', $widgetId);
                    unset($image['imageSmall']);
                }


            };
        }

        return $data;
    }

    private function migrateIpLogoGallery($widgetId, $data)
    {
        if (isset($data['logos']) && is_array($data['logos'])) {
            foreach($data['logos'] as $logoKey => $logo) {
                if (!is_array($logo)) {
                    continue;
                }
                if (isset($logo['logoSmall']) && $logo['logoSmall']) {
                    \Modules\administrator\repository\Model::unbindFile($logo['logoSmall'], 'standard/content_management', $widgetId);
                    unset($logo['logoSmall']);
                }
            };
        }
        return $data;
    }

    private function migrateIpTextImage($widgetId, $data)
    {
        if (isset($data['imageBig']) && $data['imageBig']) {
            \Modules\administrator\repository\Model::unbindFile($data['imageBig'], 'standard/content_management', $widgetId);
            unset($data['imageBig']);
        }
        if (isset($data['imageSmall']) && $data['imageSmall']) {
            \Modules\administrator\repository\Model::unbindFile($data['imageSmall'], 'standard/content_management', $widgetId);
            unset($data['imageSmall']);
        }
        return $data;
    }



    private function createNewDirs($cf)
    {
        $secureDir = $cf['BASE_DIR'].$cf['FILE_DIR'].'secure/';
        $secureTmpDir = $cf['BASE_DIR'].$cf['FILE_DIR'].'secure/tmp/';
        $manualDir = $cf['BASE_DIR'].$cf['FILE_DIR'].'manual/';

        if (!file_exists($secureDir) || !is_dir($secureDir)) {
            mkdir($secureDir);
        }
        if (!file_exists($secureTmpDir) || !is_dir($secureTmpDir)) {
            mkdir($secureTmpDir);
        }
        if (!file_exists($manualDir) || !is_dir($manualDir)) {
            mkdir($manualDir);
        }

        if (empty($cf['SECURE_DIR'])) {

            $ipConfigPath = $cf['BASE_DIR'].'ip_config.php';
            $fh = fopen($ipConfigPath, 'a');
            if (!$fh) {
                $errorData = array (
                    'file' => $ipConfigPath
            );
                throw new \IpUpdate\Library\UpdateException("Can't write to ".$ipConfigPath, \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
            }

            $data = "
      define('SECURE_DIR', 'file/secure/'); //directory not accessible from the Internet
      define('TMP_SECURE_DIR', 'file/secure/tmp/'); //directory for temporary files. Not accessible from the Internet.
      define('MANUAL_DIR', 'file/manual/'); //Used for TinyMCE file browser and others tools where user manually controls all files.
";
            fwrite($fh, $data);
            fclose($fh);
        }

    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '2.6';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '2.7';
    }


    private function addReflectionTable()
    {
        $sql = "
CREATE TABLE IF NOT EXISTS `".$this->dbPref."m_administrator_repository_reflection` (
  `reflectionId` int(11) NOT NULL AUTO_INCREMENT,
  `transformFingerprint` char(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'unique cropping options key',
  `original` varchar(255) NOT NULL,
  `reflection` varchar(255) NOT NULL COMMENT 'Cropped version of image or otherwise duplicated original file.',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`reflectionId`),
  KEY `transformFingerprint` (`transformFingerprint`,`original`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Cropped versions of original image file or otherwise duplicated repository files' AUTO_INCREMENT=1 ;


        ";

        if ($this->dbh->exec($sql) === FALSE) {
            $errorInfo = $this->dbh->errorInfo();
            throw new \IpUpdate\Library\UpdateException($sql." ".$errorInfo[0]." ".$errorInfo[1]." ".$errorInfo[2], \IpUpdate\Library\UpdateException::SQL);
        }

    }

    private function importParameters($file)
    {
        require (__DIR__.'/'.$file);

        if(isset($parameterGroupTitle)){
            foreach($parameterGroupTitle as $groupName => $group){
                foreach($group as $moduleName => $module){
                    foreach($module as $parameterGroupName => $value){
                        $moduleId = $this->getModuleId($groupName, $moduleName);
                        if (!$moduleId) {
                            throw new \IpUpdate\Library\UpdateException("Parameter import failure", \IpUpdate\Library\UpdateException::UNKNOWN);
                        }
                        $parametersGroup = $this->getParameterGroup($moduleId, $parameterGroupName);
                        if (!$parametersGroup) {
                            $admin = $parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName];
                            $groupId = $this->addParameterGroup($moduleId, $parameterGroupName, $value, $admin);
                        }
                    }
                }
            }
        }


        if(isset($parameterValue)){
            foreach($parameterValue as $groupName => $moduleGroup){
                foreach($moduleGroup as $moduleName => $module){
                    $moduleId = $this->getModuleId($groupName, $moduleName);
                    if (!$moduleId) {
                        throw new \IpUpdate\Library\UpdateException("Parameter import failure", \IpUpdate\Library\UpdateException::UNKNOWN);
                    }

                    foreach($module as $parameterGroupName => $parameterGroup){
                        $curParametersGroup = $this->getParameterGroup($moduleId, $parameterGroupName);
                        if (!$curParametersGroup) {
                            throw new \IpUpdate\Library\UpdateException("Parameter import failure", \IpUpdate\Library\UpdateException::UNKNOWN);
                        }

                        foreach($parameterGroup as $parameterName => $value){

                            if(!$this->getParameter($groupName, $moduleName, $parameterGroupName, $parameterName)) {

                                $parameter = array();
                                $parameter['name'] = $parameterName;
                                if(isset($parameterAdmin[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                                    $parameter['admin'] = $parameterAdmin[$groupName][$moduleName][$parameterGroupName][$parameterName];
                                else
                                    $parameter['admin'] = 1;

                                if(isset($parameterTitle[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                                    $parameter['translation'] = $parameterTitle[$groupName][$moduleName][$parameterGroupName][$parameterName];
                                else
                                    $parameter['translation'] = $parameterName;

                                if(isset($parameterType[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                                    $parameter['type'] = $parameterType[$groupName][$moduleName][$parameterGroupName][$parameterName];
                                else
                                    $parameter['type'] = 'string';

                                $parameter['value'] = str_replace("\r\n", "\n", $value); //user can edit parameters file and change line endings. So, we convert them back
                                $parameter['value'] = str_replace("\r", "\n", $parameter['value']);
                                $this->insertParameter($curParametersGroup['id'], $parameter);

                            }

                        }
                    }

                }
            }
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


    private function insertParameter($groupId, $parameter)
    {
        $sql = "
        INSERT INTO
            `".$this->dbPref."parameter`
        SET
            `name` = '".mysql_real_escape_string($parameter['name'])."',
            `admin` = '".mysql_real_escape_string($parameter['admin'])."',
            `group_id` = ".(int)$groupId.",
            `translation` = '".mysql_real_escape_string($parameter['translation'])."',
            `type` = '".mysql_real_escape_string($parameter['type'])."'";

        $rs = mysql_query($sql);
        if(!$rs) {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
        }
        $last_insert_id = mysql_insert_id();
        switch($parameter['type']) {
            case "string_wysiwyg":
                $sql = "insert into `".$this->dbPref."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                }
                break;
            case "string":
                $sql = "insert into `".$this->dbPref."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                }
                break;
            case "integer":
                $sql = "insert into `".$this->dbPref."par_integer` set `value` = ".mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                }
                break;
            case "bool":
                $sql = "insert into `".$this->dbPref."par_bool` set `value` = ".mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                }
                break;
            case "textarea":
                $sql = "insert into `".$this->dbPref."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs) {
                    throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                }
                break;

            case "lang":
                $languages = $this->getLanguages();
                foreach($languages as $key => $language) {
                    $sql3 = "insert into `".$this->dbPref."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3) {
                        throw new \IpUpdate\Library\UpdateException($sql3." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    }
                }
                break;
            case "lang_textarea":
                $languages = $this->getLanguages();
                foreach($languages as $key => $language) {
                    $sql3 = "insert into `".$this->dbPref."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3) {
                        throw new \IpUpdate\Library\UpdateException($sql3." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    }
                }
                break;
            case "lang_wysiwyg":
                $languages = $this->getLanguages();
                foreach($languages as $key => $language) {
                    $sql3 = "insert into `".$this->dbPref."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3) {
                        throw new \IpUpdate\Library\UpdateException($sql3." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
                    }
                }
                break;
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

    /**
     *
     *  Returns $data encoded in UTF8. Very useful before json_encode as it fails if some strings are not utf8 encoded
     * @param mixed $dat array or string
     * @return array
     */
    private function checkEncoding($dat)
    {
        if (is_string($dat)) {
            if (mb_check_encoding($dat, 'UTF-8')) {
                return $dat;
            } else {
                return utf8_encode($dat);
            }
        }
        if (is_array($dat)) {
            $answer = array();
            foreach ($dat as $i => $d) {
                $answer[$i] = $this->checkEncoding($d);
            }
            return $answer;
        }
        return $dat;
    }


}
