<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Ip;


if (!defined('CMS')) exit;

/**
 *
 * Event dispatcher class
 *
 */
class Backend{

    public static function userId(){
        if(isset($_SESSION['backend_session']['user_id'])){
            return $_SESSION['backend_session']['user_id'];
        }else {
            return false;
        }
    }

    public static function loggedIn(){
        return isset($_SESSION['backend_session']['user_id']) && $_SESSION['backend_session']['user_id'] != null;
    }

    public static function logout(){
        if(isset($_SESSION['backend_session']['user_id'])){
            unset($_SESSION['backend_session']['user_id']);
        }
        if(isset($_SESSION['backend_session'])){
            unset($_SESSION['backend_session']);
        }
        session_destroy();
    }

    public static function securityToken(){//used against CSRF atacks
        if(!isset($_SESSION['backend_session']['security_token'])){
            $_SESSION['backend_session']['security_token'] =  md5(uniqid(rand(), true));
        }
        return $_SESSION['backend_session']['security_token'];
    }

    public static function login($id){
        $_SESSION['backend_session']['user_id'] = $id;
    }

    public static function userHasPermission($userId, $moduleGroup, $moduleName) {

        $module = self::getModule($moduleGroup, $moduleName);
        
        if (!$module) {
            return false;
        }

        $sql = "select * from `".DB_PREF."user_to_mod` where `user_id`='".mysql_real_escape_string($userId)."' and `module_id`='".mysql_real_escape_string($module['id'])."' ";
        $rs = mysql_query($sql);
        if($rs) {
            if(mysql_num_rows($rs) > 0)
            return true;
            else
            return false;
        }else {
            trigger_error($sql." ".mysql_error());
        }
        return false;
    }

    public static function getModule($groupName=null , $moduleName = null){
        $sql = "select m.translation as m_translation, m.core, m.id, g.name as g_name, g.translation as g_translation, m.name as m_name, m.version from `".DB_PREF."module_group` g, `".DB_PREF."module` m where g.name = '".mysql_real_escape_string($groupName)."' and m.group_id = g.id and m.name= '".mysql_real_escape_string($moduleName)."' order by g.row_number, m.row_number limit 1";
        $rs = mysql_query($sql);
        if ($rs) {
            if($lock = mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            trigger_error($sql." ".mysql_error());
            return false;
        }
    }



}