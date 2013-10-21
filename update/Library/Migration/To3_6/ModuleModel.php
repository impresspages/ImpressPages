<?php

namespace IpUpdate\Library\Migration\To3_6;

use IpUpdate\Library\UpdateException;

class ModuleModel
{
    private $conn;
    private $dbPref;

    public function __construct($conn, $dbPref)
    {
        $this->conn = $conn;
        $this->dbPref = $dbPref;
    }

    public function getModuleGroup($name){
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
        $rs = mysql_query($sql, $this->conn);
        if($rs){
            return mysql_insert_id();
        } else {
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        }
    }

    public function getGroupModules($groupId){
        $sql = "SELECT * FROM `".$this->dbPref."module`
        WHERE
        group_id = '".(int)$groupId."'
        ORDER BY
        row_number ASC
        ";
        $rs = mysql_query($sql, $this->conn);
        if(!$rs){
            throw new \IpUpdate\Library\UpdateException($sql." ".mysql_error(), \IpUpdate\Library\UpdateException::SQL);
            return false;
        } else {
            $answer = array();
            while($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
            return $answer;
        }
    }


    public function getModuleId($group_name, $module_name){
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

}