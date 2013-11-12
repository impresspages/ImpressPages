<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Backend;

class Session
{
    function __construct()
    {

        if (sizeof($_POST) > 0 || sizeof($_GET) > 0) { //CSRF atack check
            if (
                (!isset($_REQUEST['security_token']) || $this->securityToken() != $_REQUEST['security_token'])
                &&
                (!isset($_REQUEST['action']) || $_REQUEST['action'] != "login" || isset($_REQUEST['module_id']))
            ) {
                global $cms;
                echo '
        <script type="text/javascript">document.location=\'admin.php\'</script>
        ';
                /*        trigger_error("Possible CSRF atack.\n Referer:".(isset($_SERVER['HTTP_REFERER'])?"No":$_SERVER["http_referer"])."\n Destination:".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);*/
                \Ip\Internal\Deprecated\Db::disconnect();
                exit;
            }
        }
    }

    function userId()
    {
        if (isset($_SESSION['backend_session']['userId'])) {
            return $_SESSION['backend_session']['userId'];
        } else {
            return false;
        }
    }

    function loggedIn()
    {
        return isset($_SESSION['backend_session']['userId']) && $_SESSION['backend_session']['userId'] != null;
    }

    function logout()
    {
        $this->user = null;
        if (isset($_SESSION['backend_session']['userId'])) {
            unset($_SESSION['backend_session']['userId']);
        }
        if (isset($_SESSION['backend_session'])) {
            unset($_SESSION['backend_session']);
        }
        session_destroy();
    }

    function securityToken()
    { //used against CSRF atacks
        if (empty($_SESSION['ipSecurityToken'])) {
            $_SESSION['ipSecurityToken'] = md5(uniqid(rand(), true));
        }
        return $_SESSION['ipSecurityToken'];
    }

    function login($id)
    {
        $_SESSION['backend_session']['userId'] = $id;
    }


}

