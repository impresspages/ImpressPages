<?php
namespace Ip\Module\Admin;


class OldSessionInterface{

    function __construct(){


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
        $session = \Ip\ServiceLocator::getSession();
        return $session->getSecurityToken();

    }

    function login($id){
        $_SESSION['backend_session']['user_id'] = $id;
    }




}