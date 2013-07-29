<?php

namespace IpUpdate\Library\Migration\To3_6;

use IpUpdate\Library\UpdateException;

class UserModel
{
    private $conn;
    private $dbPref;

    public function __construct($conn, $dbPref)
    {
        $this->conn = $conn;
        $this->dbPref = $dbPref;
    }

    public function getUsers(){
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

    public function addPermissions($moduleId, $userId){
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

}