<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

/**
 * Logging in a user and checking login status
 *
 */
class User
{

    /**
     * @return bool true if user is logged in
     */
    function loggedIn()
    {
        return isset($_SESSION['ipUserId']);
    }

    /**
     * Logout current user
     * @return void
     */
    function logout()
    {
        if (isset($_SESSION['ipUserId'])) {
            ipEvent('ipBeforeUserLogout', array('userId' => $this->userId()));
            unset($_SESSION['ipUserId']);
            ipEvent('ipUserLogout', array('userId' => $this->userId()));
        }
    }

    /**
     * Get current user ID
     * @return int Logged in user ID or false, if user is not logged in
     */
    function userId()
    {
        if (isset($_SESSION['ipUserId'])) {
            return $_SESSION['ipUserId'];
        } else {
            return false;
        }
    }

    /**
     * Set user as logged in
     * @param int $id User id
     * @return void
     */
    function login($id)
    {
        ipEvent('ipUserLogin', array('userId' => $id));
        $_SESSION['ipUserId'] = $id;
    }


}
