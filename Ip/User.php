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
        if (isset($_SESSION['ipFrontend']['userId'])) {
            return $_SESSION['ipFrontend']['userId'];
        } else {
            return false;
        }
    }

    /**
     * @return bool true if user is logged in
     */
    function loggedIn(){
        return isset($_SESSION['ipFrontend']['userId']);
    }

    /**
     * User logout
     * @return void
     */
    function logout(){
        if(isset($_SESSION['ipFrontend']['userId'])) {
            $dispatcher = \Ip\ServiceLocator::getDispatcher();
            $dispatcher->notify(new \Ip\Event(null, 'ipUserLogout',  array('userId'=>$_SESSION['ipFrontend']['userId'])));
            unset($_SESSION['ipFrontend']['userId']);
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
        $_SESSION['ipFrontend']['userId'] = $id;
    }




}
