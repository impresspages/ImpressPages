<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Backend;

if(!defined('BACKEND')) exit;

class Session{
    function __construct(){
        session_name(SESSION_NAME);
        session_start();

        if(sizeof($_POST)>0 || sizeof($_GET)>0){ //CSRF atack check
            if(
            (!isset($_REQUEST['security_token']) || $this->securityToken() != $_REQUEST['security_token'])
            &&
            (!isset($_REQUEST['action']) || $_REQUEST['action'] != "login" || isset($_REQUEST['module_id']))
            ){
                global $cms;
                echo '
        <script type="text/javascript">document.location=\'admin.php\'</script>
        ';
                /*        trigger_error("Possible CSRF atack.\n Referer:".(isset($_SERVER['HTTP_REFERER'])?"No":$_SERVER["http_referer"])."\n Destination:".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);*/
                \Db::disconnect();
                exit;
            }
        }
    }

    function userId(){
        if(isset($_SESSION['backend_session']['user_id']))
        return $_SESSION['backend_session']['user_id'];
        else
        return false;
    }

    function loggedIn(){
        return isset($_SESSION['backend_session']['user_id']) && $_SESSION['backend_session']['user_id'] != null;
    }

    function logout(){
        $this->user = null;
        if(isset($_SESSION['backend_session']['user_id']))
        unset($_SESSION['backend_session']['user_id']);
        if(isset($_SESSION['backend_session']))
        unset($_SESSION['backend_session']);
        session_destroy();
    }

    function securityToken(){//used against CSRF atacks
        if(!isset($_SESSION['backend_session']['security_token'])){
            $_SESSION['backend_session']['security_token'] =  md5(uniqid(rand(), true));
        }
        return $_SESSION['backend_session']['security_token'];
    }

    function login($id){
        $_SESSION['backend_session']['user_id'] = $id;
    }




}

