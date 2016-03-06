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
     * Alias of isLoggedIn
     * @private
     * @return bool true if user is logged in
     */
    function loggedIn()
    {
        return $this->isLoggedIn();
    }

    /**
     * @return bool true if user is logged in
     */
    function isLoggedIn()
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


    /**
     * Get all user info collected from all user specific plugins.
     * @param int $userId
     * @return array
     */
    function data($userId = null)
    {
        if ($userId === null) {
            $userId = $this->userId();
        }
        if (!$userId) {
            return array();
        }
        $info = array(
            'userId' => $userId
        );
        $data = array(
            'id' => $userId
        );
        return ipFilter('ipUserData', $data, $info);
    }

}
