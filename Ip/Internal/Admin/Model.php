<?php
namespace Ip\Internal\Admin;


class Model
{

    protected $errors = [];

    protected function __construct()
    {

    }

    /**
     * @param \Ip\Request $request
     * @return bool
     */
    public static function isLoginPage(\Ip\Request $request = null)
    {
        if ($request == null) {
            $request = ipRequest();
        }
        $relativePath = $request->getRelativePath();
        return in_array($relativePath, array('admin', 'admin/', 'admin.php', 'admin.php/'));
    }

    public static function isPasswordResetPage(\Ip\Request $request = null)
    {
        if ($request == null) {
            $request = ipRequest();
        }
        $sa = $request->getQuery('sa');
        return in_array($sa, array('Admin.passwordResetForm', 'Admin.passwordReset'));
    }

    /**
     * @return Model
     */
    public static function instance()
    {
        return new Model();
    }

    /**
     * @param string $currentModule Name of the current (active) module
     * @return \Ip\Internal\Admin\MenuItem[]
     */
    public function getAdminMenuItems($currentModule)
    {
        $answer = [];


        $modules = \Ip\Internal\Plugins\Model::getModules();
        foreach ($modules as $module) {

            // skipping modules that don't have 'index' (default) action in AdminController
            $controllerClass = 'Ip\\Internal\\' . $module . '\\AdminController';
            if (!class_exists($controllerClass) || !method_exists($controllerClass, 'index')) {
                continue;
            }

            $moduleItem = new \Ip\Internal\Admin\MenuItem();
            $moduleItem->setTitle(__($module, 'Ip-admin', false));
            $moduleItem->setUrl(ipActionUrl(array('aa' => $module . '.index')));
            $moduleItem->setIcon($this->getAdminMenuItemIcon($module));
            if ($module == $currentModule) {
                $moduleItem->markAsCurrent(true);
            }

            if (ipAdminPermission($module)) {
                $answer[] = $moduleItem;
            }
        }


        $plugins = \Ip\Internal\Plugins\Service::getActivePlugins();

        foreach ($plugins as $plugin) {
            $controllerClass = '\\Plugin\\' . $plugin['name'] . '\\AdminController';
            if (!class_exists($controllerClass) || !method_exists($controllerClass, 'index')) {
                continue;
            }
            $moduleItem = new \Ip\Internal\Admin\MenuItem();
            $moduleItem->setTitle(__($plugin['title'], 'Ip-admin', false));
            $moduleItem->setUrl(ipActionUrl(array('aa' => $plugin['name'])));
            $moduleItem->setIcon($this->getAdminMenuPluginIcon($plugin['name']));
            if ($plugin['name'] == $currentModule) {
                $moduleItem->markAsCurrent(true);
            }
            if (ipAdminPermission($plugin['name'])) {
                $answer[] = $moduleItem;
            }
        }

        $answer = ipFilter('ipAdminMenu', $answer);

        return $answer;
    }
    
    /**
     * Function to get icon from plugin config
     * @param $module
     * @return string
     */
    public static function getAdminMenuPluginIcon($module)
    {
        $conf = \Ip\Internal\Plugins\Service::getPluginConfig($module);
        return isset($conf['icon']) ? $conf['icon'] : 'fa-cog';
    }

    public static function getAdminMenuItemIcon($module)
    {
        $icon = 'fa-cog'; // default

        switch ($module) {
            case 'Content':
                $icon = 'fa-pencil-square-o';
                break;
            case 'Pages':
                $icon = 'fa-sitemap';
                break;
            case 'Administrators':
                $icon = 'fa-users';
                break;
            case 'Design':
                $icon = 'fa-pencil';
                break;
            case 'Plugins':
                $icon = 'fa-code-fork';
                break;
            case 'Config':
                $icon = 'fa-cog';
                break;
            case 'Languages':
                $icon = 'fa-language';
                break;
        }

        return $icon;
    }

    public static function setSafeMode($value)
    {
        $_SESSION['module']['admin']['safemode'] = (bool)$value;
    }

    public static function isSafeMode()
    {
        if (isset($_SESSION['module']['admin']['safemode'])) {
            return (bool)$_SESSION['module']['admin']['safemode'];
        }
        return false;
    }

    public static function getUserId()
    {
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
            ipLog()->notice(
                'Admin.loginPrevented: {username} from {ip}',
                array('username' => $username, 'ip' => ipRequest()->getServer('REMOTE_ADDR'))
            );
            return false;
        }


        $administrator = \Ip\Internal\Administrators\Service::getByUsername($username);
        if (!$administrator) {
            \Ip\ServiceLocator::dispatcher()->event(
                'ipAdminLoginFailed',
                array('username' => $username, 'ip' => ipRequest()->getServer('REMOTE_ADDR'))
            );
            ipLog()->info('Admin.incorrectLogin: {username} from {ip}', array('username' => $username, 'ip' => $ip));
            $this->errors = array('login' => __('Following user doesn\'t exist', 'Ip-admin'));
            return false;
        }

        if (\Ip\Internal\Administrators\Service::checkPassword($administrator['id'], $password)) {
            Service::setAdminLogin($username);
            return true;
        } else {
            \Ip\ServiceLocator::dispatcher()->event(
                'ipAdminLoginFailed',
                array('username' => $username, 'ip' => ipRequest()->getServer('REMOTE_ADDR'))
            );
            ipLog()->info('Admin.incorrectLogin: {username} from {ip}', array('username' => $username, 'ip' => $ip));
            $this->errors = array('password' => __('Incorrect password', 'Ip-admin'));
            return false;
        }
    }

    public function setAdminLogin($username)
    {
        $administrator = \Ip\Internal\Administrators\Service::getByUsername($username);
        $ip = ipRequest()->getServer('REMOTE_ADDR');
        Backend::login($administrator['id']);
        ipEvent('ipAdminLoginSuccessful', array('username' => $username, 'id' => $administrator['id']));
        ipLog()->info('Admin.loggedIn: {username} from {ip}', array('username' => $username, 'ip' => $ip));

    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function logout()
    {
        Backend::logout();
    }

    public static function randString($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }

}
