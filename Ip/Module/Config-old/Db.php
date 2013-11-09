<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Config;

class Db{

    function deletePermissions($moduleId){
        $sql = "delete from `".DB_PREF."user_to_mod` where `module_id` = '".ip_deprecated_mysql_real_escape_string($moduleId)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs)
        trigger_error($sql);
    }

    function addPermissions($moduleId, $userId){
        $sql = "insert into `".DB_PREF."user_to_mod` set `module_id` = '".ip_deprecated_mysql_real_escape_string($moduleId)."', `user_id` = '".ip_deprecated_mysql_real_escape_string($userId)."'";
        $rs = ip_deprecated_mysql_query($sql);
        if(!$rs)
        trigger_error($sql);
    }

}

