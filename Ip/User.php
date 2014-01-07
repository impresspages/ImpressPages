<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

/**
 * Website language class
 * @package ImpressPages
 */
class User{

    /**
     * @return int loggedIn user id or false
     */
    function userId(){
        if (isset($_SESSION['ipUser']['userId'])) {
            return $_SESSION['ipUser']['userId'];
        } else {
            return false;
        }
    }

    /**
     * @return bool true if user is logged in
     */
    function loggedIn(){
        return isset($_SESSION['ipUser']['userId']);
    }

    /**
     * User logout
     * @return void
     */
    function logout(){
        if(isset($_SESSION['ipUser']['userId'])) {
            ipDispatcher()->notify('Ip.beforeUserLogout',  array('userId'=>$this->userId()));
            unset($_SESSION['ipUser']['userId']);
            ipDispatcher()->notify('ipUserLogout',  array('userId'=>$this->userId()));
        }
    }

    /**
     * User login
     * @param int $id user id
     * @return void
     */
    function login($id){
        ipDispatcher()->notify('ipUserLogin',  array('userId'=>$id));
        $_SESSION['ipUser']['userId'] = $id;
    }




}
