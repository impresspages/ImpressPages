<?php
namespace Ip\Internal\Admin;


class Model{

    protected $errors = array();

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
            if (in_array($module, array('Log', 'Email'))) {
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

    public static function getUserId(){
        if (isset($_SESSION['backend_session']['userId'])) {
            return $_SESSION['backend_session']['userId'];
        } else {
            return false;
        }
    }

    public function login($username, $password)
    {
        $ip = ipRequest()->getServer('REMOTE_ADDR');

        $preventReason = ipJob('ipAdminLoginPrevent', array('username' => $username));
        if ($preventReason) {
            $this->errors = array('global_error' => $preventReason);
            ipLog()->notice('Admin.loginPrevented: {username} from {ip}', array('username' => $username, 'ip' => ipRequest()->getServer('REMOTE_ADDR')));
            return false;
        }




        $administrator = \Ip\Internal\Administrators\Service::getByUsername($username);
        if (!$administrator) {
            \Ip\ServiceLocator::dispatcher()->event('ipAdminLoginFailed', array('username' => $username, 'ip' => ipRequest()->getServer('REMOTE_ADDR')));
            ipLog()->info('Admin.incorrectLogin: {username} from {ip}', array('username' => $username, 'ip' => $ip));
            $this->errors = array('login' => __('Following user doesn\'t exist', 'ipAdmin'));
            return false;
        }

        if (\Ip\Internal\Administrators\Service::checkPassword($administrator['id'], $password)) {
            $_SESSION['backend_session']['userId'] = $administrator['id'];
            \Ip\ServiceLocator::dispatcher()->event('ipAdminLoginSuccessful', array('userId' => $administrator['id']));
            ipLog()->info('Admin.loggedIn: {username} from {ip}', array('username' => $username, 'ip' => $ip));
            return true;
        } else {
            \Ip\ServiceLocator::dispatcher()->event('ipAdminLoginFailed', array('username' => $username, 'ip' => ipRequest()->getServer('REMOTE_ADDR')));
            ipLog()->info('Admin.incorrectLogin: {username} from {ip}', array('username' => $username, 'ip' => $ip));
            $this->errors = array('password' => __('Incorrect password', 'ipAdmin'));
            return false;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function logout()
    {
        if(isset($_SESSION['backend_session']))
            unset($_SESSION['backend_session']);
    }



}
