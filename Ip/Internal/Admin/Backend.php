<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Admin;

/**
 *
 * Event dispatcher class
 *
 */
class Backend{

    public static function userId(){
        if(isset($_SESSION['backend_session']['userId'])){
            return $_SESSION['backend_session']['userId'];
        }else {
            return false;
        }
    }

    public static function loggedIn(){
        return isset($_SESSION['backend_session']['userId']) && $_SESSION['backend_session']['userId'] != null;
    }

    public static function logout(){
        if(isset($_SESSION['backend_session']['userId'])){
            unset($_SESSION['backend_session']['userId']);
        }
        if(isset($_SESSION['backend_session'])){
            unset($_SESSION['backend_session']);
        }
        session_destroy();
    }

    public static function login($id){
        $_SESSION['backend_session']['userId'] = $id;
    }

    public static function userHasPermission($userId, $moduleGroup, $moduleName) {

        $module = self::getModule($moduleGroup, $moduleName);
        
        if (!$module) {
            return false;
        }

        if (!$module['managed']) {
            return true; //undamaged modules have no managed permissions
        }



        $sql = "select * from `".DB_PREF."user_to_mod` where `userId`='".ip_deprecated_mysql_real_escape_string($userId)."' and `module_id`='".ip_deprecated_mysql_real_escape_string($module['id'])."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            if(ip_deprecated_mysql_num_rows($rs) > 0) {
                return true;
            } else {
                return false;
            }
        }else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
        }
        return false;
    }

    public static function getModule($groupName=null , $moduleName = null){
        $sql = "select m.translation as m_translation, m.core, m.id, g.name as g_name, g.translation as g_translation, m.name as m_name, m.version, m.managed from `".DB_PREF."module_group` g, `".DB_PREF."module` m where g.name = '".ip_deprecated_mysql_real_escape_string($groupName)."' and m.group_id = g.id and m.name= '".ip_deprecated_mysql_real_escape_string($moduleName)."' order by g.row_number, m.row_number limit 1";
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            trigger_error($sql." ".ip_deprecated_mysql_error());
            return false;
        }
    }



}