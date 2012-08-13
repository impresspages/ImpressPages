<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

namespace IpUpdate\Library\Migration\To2_4;


class Script extends \IpUpdate\Library\Migration\General{

    private $conn;
    private $dbPref;

    public function process($cf)
    {
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);
        $this->conn = $conn;
        $this->dbPref = $cf['DB_PREF'];


        $moduleId = $this->getModuleId('standard', 'configuration');
        $parametersGroup = $this->getParameterGroup($moduleId, 'admin_translations');
        if ($parametersGroup) {
            $groupId = $parametersGroup['id'];
        } else {
            $groupId = $this->addParameterGroup($moduleId, 'admin_translations', 'Admin translations', 1);
        }

            if(!$this->getParameter('standard', 'configuration', 'admin_translations', 'edit')) {
                $this->addStringParameter($groupId, 'Edit', 'edit', 'Edit', 1);
            }

            if(!$this->getParameter('standard', 'configuration', 'admin_translations', 'confirm')) {
                $this->addStringParameter($groupId, 'Confirm', 'confirm', 'Confirm', 1);
            }

            if(!$this->getParameter('standard', 'configuration', 'admin_translations', 'cancel')) {
                $this->addStringParameter($groupId, 'Cancel', 'cancel', 'Cancel', 1);
            }





        $moduleId = $this->getModuleId('developer', 'inline_management');

        if ($moduleId === false) {
            $moduleGroup = $this->getModuleGroup('developer');
            $moduleId = $this->addModule($moduleGroup['id'], 'Inline Management', 'inline_management', true, false, true, '1.00');
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

            if(!$this->getParameter('developer', 'inline_management', 'admin_translations', 'type_text')) {
                $this->addStringParameter($groupId, 'Text logo', 'type_text', 'Text logo', 1);
            }

            if(!$this->getParameter('developer', 'inline_management', 'admin_translations', 'type_image')) {
                $this->addStringParameter($groupId, 'Image logo', 'type_image', 'Image logo', 1);
            }

            if(!$this->getParameter('developer', 'inline_management', 'admin_translations', 'default')) {
                $this->addStringParameter($groupId, 'Default', 'default', 'Default', 1);
            }

        $parametersGroup = $this->getParameterGroup($moduleId, 'options');
        if ($parametersGroup) {
            $groupId = $parametersGroup['id'];
        } else {
            $groupId = $this->addParameterGroup($moduleId, 'options', 'Options', 1);
        }
            if(!$this->getParameter('developer', 'inline_management', 'options', 'available_fonts')) {
                $fonts = 
'Arial,Arial,Helvetica,sans-serif
Arial Black,Arial Black,Gadget,sans-serif
Courier New,Courier New,Courier,monospace
Georgia,Georgia,serif
Impact,Charcoal,sans-serif
Lucida Console,Monaco,monospace
Lucida Sans Unicode,Lucida Grande,sans-serif
Palatino Linotype,Book Antiqua,Palatino,serif
Tahoma,Geneva,sans-serif
Times New Roman,Times,serif
Trebuchet MS,Helvetica,sans-serif
Verdana,Geneva,sans-serif
Gill Sans,Geneva,sans-serif';
                $this->addTextareaParameter($groupId, 'Available fonts', 'available_fonts', $fonts, 1);
            }

    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '2.3';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '2.4';
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

    public function addModule($groupId, $moduleTranslation, $moduleName, $admin, $managed, $core, $version, $rowNumber = 0){
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
        $rs = mysql_query($sql);
        if($rs){
            return mysql_insert_id();
        } else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }

    public function getModuleGroup($name){
        $sql = "select * from `".$this->dbPref."module_group` where name = '".mysql_real_escape_string($name)."' ";
        $rs = mysql_query($sql);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){
                return $lock;
            } else {
                return false;
            }
        } else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }

    public function getUsers(){
        $answer = array();
        $sql = "select * from `".$this->dbPref."user` where 1";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                $answer[] = $lock;
            }
            return $answer;
        } else {
            trigger_error($sql." ".mysql_error());
            return false;
        }

    }

    public function addPermissions($moduleId, $userId){
        $sql = "insert into `".$this->dbPref."user_to_mod`
    set
    module_id = '".(int)$moduleId."',
    user_id = '".(int)$userId."'

    ";
        $rs = mysql_query($sql);
        if($rs){
            return mysql_insert_id();
        } else {
            trigger_error($sql." ".mysql_error());
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


    private function addTextareaParameter($groupId, $translation, $name, $value, $admin)
    {
        $sql = "INSERT INTO `".$this->dbPref."parameter` (`name`, `admin`, `regexpression`, `group_id`, `translation`, `comment`, `type`)
        VALUES ('".mysql_real_escape_string($name)."', ".(int)$admin.", '', ".(int)$groupId.", '".mysql_real_escape_string($translation)."', NULL, 'textarea')";
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

}
