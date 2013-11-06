<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Log;

class Db {


    public static function log($module, $name, $value_str = null, $value_int= null, $value_float= null) {
        if($value_int === null)
        $value_int = 'NULL';
        else
        $value_int = (int)$value_int;

        if($value_float === null)
        $value_float = 'NULL';
        else
        $value_float = "'".ip_deprecated_mysql_real_escape_string($value_float)."'";

        $sql = "insert into `".DB_PREF."log` set `module` = '".ip_deprecated_mysql_real_escape_string($module)."', `name` = '".ip_deprecated_mysql_real_escape_string($name)."', value_str='".ip_deprecated_mysql_real_escape_string($value_str)."', value_int=".$value_int.", value_float=".$value_float."  ";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs) {
            echo $sql." ".ip_deprecated_mysql_error(); //can't use standard error handling because of infinite loop danger
        }
    }


    public static function lastLogs($minutes, $module = null, $name = null) {
        if ($module)
        $moduleSql = "`module` = '".ip_deprecated_mysql_real_escape_string($module)."' and ";
        else
        $moduleSql = '';

        if ($name)
        $nameSql = "`name` = '".ip_deprecated_mysql_real_escape_string($name)."' and ";
        else
        $nameSql = '';

        $sql = "select * from `".DB_PREF."log` where ".$moduleSql." ".$nameSql." ".((int)$minutes)." > TIMESTAMPDIFF(MINUTE,`time`,NOW())  ";

        $rs = ip_deprecated_mysql_query($sql);
        $answer = array();
        if($rs) {
            while($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                $answer[] = $lock;
            }
            return $answer;
        } else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function lastLogsCount($minutes, $module = null, $name = null) {
        if ($module)
        $moduleSql = "`module` = '".ip_deprecated_mysql_real_escape_string($module)."' and ";
        else
        $moduleSql = '';

        if ($name)
        $nameSql = "`name` = '".ip_deprecated_mysql_real_escape_string($name)."' and ";
        else
        $nameSql = '';

        $sql = "select count(*) as log_count from `".DB_PREF."log` where ".$moduleSql." ".$nameSql." ".((int)$minutes)." > TIMESTAMPDIFF(MINUTE,`time`,NOW())  ";

        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            $lock = ip_deprecated_mysql_fetch_assoc($rs);
            if($lock)
            return $lock['log_count'];
        }else trigger_error($sql." ".ip_deprecated_mysql_error());
    }


    public static function deleteOldLogs($days) {
        $sql = "delete from `".DB_PREF."log` where  (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`time`)) > ".($days*24*60*60)."";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs)
        trigger_error($sql." ".ip_deprecated_mysql_error());
    }

}



