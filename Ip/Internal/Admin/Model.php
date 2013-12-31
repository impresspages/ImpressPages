<?php
namespace Ip\Internal\Admin;


class Model{


    protected function __construct()
    {

    }

    /**
    * @return Model
    */
    public static function instance()
    {
        return new Model();
    }

    public function getAdminMenuItems()
    {
        $answer = array();


        $modules = \Ip\Internal\Plugins\Model::getModules();
        foreach($modules as $module) {
            if (in_array($module, array('Languages', 'Log', 'Email'))) {
                continue;
            }
            $controllerClass = 'Ip\\Internal\\'.$module.'\\AdminController';
            if (!class_exists($controllerClass) || !method_exists($controllerClass, 'index')) {
                continue;
            }
            $moduleItem = new \Ip\Menu\Item();
            $moduleItem->setTitle($module);
            $moduleItem->setUrl(\Ip\Internal\Deprecated\Url::generate(null, null, null, array('aa' => $module.'.index')));
            $moduleItem->setUrl(ipActionUrl(array('aa' => $module . '.index')));
            $answer[] = $moduleItem;
        }



        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();

        foreach($plugins as $plugin) {
            $controllerClass = '\\Plugin\\' . $plugin . '\\AdminController';
            if (!class_exists($controllerClass) || !method_exists($controllerClass, 'index')) {
                continue;
            }
            $moduleItem = new \Ip\Menu\Item();
            $moduleItem->setTitle($plugin);
            $moduleItem->setUrl(\Ip\Internal\Deprecated\Url::generate(null, null, null, array('aa' => $plugin.'.index')));
            $moduleItem->setUrl(ipActionUrl(array('aa' => $plugin . '.index')));
            $answer[] = $moduleItem;
        }

        return $answer;
    }

    public static function setSafeMode($value)
    {
        $_SESSION['module']['admin']['safemode'] = (bool) $value;
    }

    public static function isSafeMode()
    {
        if (isset($_SESSION['module']['admin']['safemode'])) {
            return (bool) $_SESSION['module']['admin']['safemode'];
        }
        return false;
    }

    public function getUserId(){
        if(isset($_SESSION['backend_session']['userId']))
            return $_SESSION['backend_session']['userId'];
        else
            return false;
    }

    public function login($username, $password)
    {
        $ip = ipRequest()->getServer('REMOTE_ADDR');

        // TODO use events for that
        if($this->incorrectLoginCount($username.'('.$ip.')') > 2) {
            $this->loginError = __('Your login suspended for one hour.', 'ipAdmin');
            ipLog()->warning('Admin.loginSuspended: {username} from {ip}', array('username' => $username, 'ip' => $ip));
        } else {
            $id = $this->userId($username, $password);
            if($id !== false) {
                $_SESSION['backend_session']['userId'] = $id;
                \Ip\ServiceLocator::dispatcher()->notify('Admin.login', array('userId' => $id));

                ipLog()->info('Admin.loggedIn: {username} from {ip}', array('username' => $username, 'ip' => $ip));
                return true;
            } else {
                $this->loginError = __('Incorrect name or password', 'ipAdmin');
                ipLog()->info('Admin.incorrectLogin: {username} from {ip}', array('username' => $username, 'ip' => $ip));
                return false;
            }
        }
    }

    public function logout()
    {
        if(isset($_SESSION['backend_session']))
            unset($_SESSION['backend_session']);
    }

    protected function incorrectLoginCount($userName)
    {
        return 0;
    }

    protected  function userId($name, $pass) {
        $answer = false;
        $sql = "select id from " . ipTable('user') . " where `name` = '".ip_deprecated_mysql_real_escape_string($name)."' and `pass`='".md5($pass)."' and not blocked ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs) {
            if($lock = ip_deprecated_mysql_fetch_assoc($rs))
                $answer = $lock['id'];
        }else trigger_error($sql." ".ip_deprecated_mysql_error());
        return $answer;
    }
}