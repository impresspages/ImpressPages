<?php
namespace Ip\Module\Admin;


class OldSessionInterface{

    function __construct(){


    }

    function userId(){
        if(isset($_SESSION['backend_session']['userId']))
            return $_SESSION['backend_session']['userId'];
        else
            return false;
    }

    function loggedIn(){
        return isset($_SESSION['backend_session']['userId']) && $_SESSION['backend_session']['userId'] != null;
    }

    function logout(){
        $this->user = null;
        if(isset($_SESSION['backend_session']['userId']))
            unset($_SESSION['backend_session']['userId']);
        if(isset($_SESSION['backend_session']))
            unset($_SESSION['backend_session']);
        session_destroy();
    }

    function securityToken(){//used against CSRF atacks
        $session = \Ip\ServiceLocator::getApplication();
        return $session->getSecurityToken();

    }

    function login($id){
        $_SESSION['backend_session']['userId'] = $id;
    }




}