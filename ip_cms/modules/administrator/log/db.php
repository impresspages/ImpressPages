<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\administrator\log;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

class Db {


    public static function log($module, $name, $value_str = null, $value_int= null, $value_float= null) {
        if($value_int === null)
        $value_int = 'NULL';
        else
        $value_int = (int)$value_int;

        if($value_float === null)
        $value_float = 'NULL';
        else
        $value_float = "'".mysql_real_escape_string($value_float)."'";

        $sql = "insert into `".DB_PREF."log` set `module` = '".mysql_real_escape_string($module)."', `name` = '".mysql_real_escape_string($name)."', value_str='".mysql_real_escape_string($value_str)."', value_int=".$value_int.", value_float=".$value_float."  ";
        $rs = mysql_query($sql);
        if(!$rs) {
            echo $sql." ".mysql_error(); //can't use standard error handling because of infinite loop danger
        }
    }


    public static function lastLogs($minutes, $module = null, $name = null) {
        if ($module)
        $moduleSql = "`module` = '".mysql_real_escape_string($module)."' and ";
        else
        $moduleSql = '';

        if ($name)
        $nameSql = "`name` = '".mysql_real_escape_string($name)."' and ";
        else
        $nameSql = '';

        $sql = "select * from `".DB_PREF."log` where ".$moduleSql." ".$nameSql." ".((int)$minutes)." > TIMESTAMPDIFF(MINUTE,`time`,NOW())  ";

        $rs = mysql_query($sql);
        $answer = array();
        if($rs) {
            while($lock = mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
            return $answer;
        } else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }

    public static function lastLogsCount($minutes, $module = null, $name = null) {
        if ($module)
        $moduleSql = "`module` = '".mysql_real_escape_string($module)."' and ";
        else
        $moduleSql = '';

        if ($name)
        $nameSql = "`name` = '".mysql_real_escape_string($name)."' and ";
        else
        $nameSql = '';

        $sql = "select count(*) as log_count from `".DB_PREF."log` where ".$moduleSql." ".$nameSql." ".((int)$minutes)." > TIMESTAMPDIFF(MINUTE,`time`,NOW())  ";

        $rs = mysql_query($sql);
        if($rs) {
            $lock = mysql_fetch_assoc($rs);
            if($lock)
            return $lock['log_count'];
        }else trigger_error($sql." ".mysql_error());
    }


    public static function deleteOldLogs($days) {
        $sql = "delete from `".DB_PREF."log` where  (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`time`)) > ".($days*24*60*60)."";
        $rs = mysql_query($sql);
        if(!$rs)
        trigger_error($sql." ".mysql_error());
    }

}



