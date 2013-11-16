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
            $dispatcher = \Ip\ServiceLocator::getDispatcher();
            $dispatcher->notify(new \Ip\Event(null, 'ipUserLogout',  array('userId'=>$_SESSION['ipUser']['userId'])));
            unset($_SESSION['ipUser']['userId']);
        }
    }
    



    /**
     * User login
     * @param int $id user id
     * @return void
     */
    function login($id){
        $dispatcher = \Ip\ServiceLocator::getDispatcher();
        $dispatcher->notify(new \Ip\Event(null, 'ipUserLogin',  array('userId'=>$id)));
        $_SESSION['ipUser']['userId'] = $id;
    }




}
