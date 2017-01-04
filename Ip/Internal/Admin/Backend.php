<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Admin;

/**
 *
 * Event dispatcher class
 *
 */
class Backend
{

    public static function userId()
    {
        if (isset($_SESSION['backend_session']['userId'])) {
            return $_SESSION['backend_session']['userId'];
        } else {
            return false;
        }
    }

    public static function loggedIn()
    {
        return isset($_SESSION['backend_session']['userId']) && $_SESSION['backend_session']['userId'] != null;
    }

    public static function logout()
    {
        $adminId = ipAdminId();
        ipEvent('ipBeforeAdminLogout', array('administratorId' => $adminId));
        if (isset($_SESSION['backend_session'])) {
            unset($_SESSION['backend_session']);
        }
        session_destroy();
        ipEvent('ipAdminLoggedOut', array('administratorId' => $adminId));
    }

    public static function login($id)
    {
        $_SESSION['backend_session']['userId'] = $id;
        session_regenerate_id(true);
        ipEvent('ipAdminLoggedIn', array('administratorId' => $id));
    }

}
