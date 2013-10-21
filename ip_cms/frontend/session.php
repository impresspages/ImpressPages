<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Frontend;

if (!defined('CMS')) exit;


/**
 * Website language class
 * @package ImpressPages
 */
class Session{

    function __construct(){
        if(session_id() == '') { //if session hasn't been started yet
            session_name(SESSION_NAME);
            session_start();
        }
    }

    /**
     * @return int loggedIn user id or false
     */
    function userId(){
        if(isset($_SESSION['frontend_session']['user_id']))
        return $_SESSION['frontend_session']['user_id'];
        else
        return false;
    }

    /**
     * @return bool true if user is logged in
     */
    function loggedIn(){
        return isset($_SESSION['frontend_session']['user_id']);
    }

    /**
     * User logout
     * @return void
     */
    function logout(){
        global $site;
        if(isset($_SESSION['frontend_session']['user_id'])) {
            $site->dispatchEvent('community', 'user', 'logout', array('user_id'=>$_SESSION['frontend_session']['user_id']));
            unset($_SESSION['frontend_session']['user_id']);
        }
    }
    



    /**
     * User login
     * @param int $id user id
     * @return void
     */
    function login($id){
        global $site;
        $site->dispatchEvent('community', 'user', 'login', array('user_id'=>$id));
        $_SESSION['frontend_session']['user_id'] = $id;
    }

    /**
     * Get security token used to prevent cros site scripting
     * @return string
     */
    public function getSecurityToken()
    {
        if (empty($_SESSION['ipSecurityToken'])) {
            $_SESSION['ipSecurityToken'] = md5(uniqid(rand(), true));
        }
        return $_SESSION['ipSecurityToken'];
    }


}
