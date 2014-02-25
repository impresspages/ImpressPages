<?php


namespace Ip\Internal\Admin;


class Event
{


    public static function ipInitFinished()
    {
        //show admin submenu if needed
        if (ipRequest()->getControllerType() == \Ip\Request::CONTROLLER_TYPE_ADMIN) {
            $submenu = Submenu::getSubmenuItems();
            if ($submenu) {
                ipResponse()->setLayoutVariable('submenu', $submenu);
            }
        }

        // Show admin toolbar if admin is logged in:
        if (ipIsManagementState() && !ipRequest()->getRequest('pa') || ipRequest()->getRequest('aa') && !empty($_SESSION['backend_session']['userId'])) {
            if (!ipRequest()->getQuery('ipDesignPreview')) {
                ipAddJs('Ip/Internal/Admin/assets/admin.js');
                ipAddJsVariable('ipAdminNavbar', static::getAdminNavbarHtml());
            }
        }
    }

    protected static function getAdminNavbarHtml()
    {
        $requestData = \Ip\ServiceLocator::request()->getRequest();
        $curModTitle = '';
        $curModUrl = '';
        $curModIcon = '';
        $helpUrl = 'http://www.impresspages.org/help2';

        if (!empty($requestData['aa'])) {
            $parts = explode('.', $requestData['aa']);
            $curModule = $parts[0];
        } elseif (ipIsManagementState()) {
            $curModule = "Content";
        }

        if (isset($curModule) && $curModule) {
            $helpUrl = 'http://www.impresspages.org/help2/' . $curModule;
            $curModTitle = __($curModule, 'ipAdmin', FALSE);
            $curModUrl = ipActionUrl(array('aa' => $curModule . '.index'));
            $curModIcon = Model::getAdminMenuItemIcon($curModule);
        }

        $navbarButtons = array(
            array(
                'text' => '',
                'hint' => __('Logout', 'ipAdmin', FALSE),
                'url' => ipActionUrl(array('sa' => 'Admin.logout')),
                'class' => 'ipsAdminLogout',
                'faIcon' => 'fa-power-off'
            )
        );

        $navbarButtons = ipFilter('ipAdminNavbarButtons', $navbarButtons);

        $navbarCenterElements = ipFilter('ipAdminNavbarCenterElements', array());

        $data = array(
            'menuItems' => Model::instance()->getAdminMenuItems($curModule),
            'curModTitle' => $curModTitle,
            'curModUrl' => $curModUrl,
            'curModIcon' => $curModIcon,
            'helpUrl' => $helpUrl,
            'navbarButtons' => array_reverse($navbarButtons),
            'navbarCenterElements' => $navbarCenterElements
        );


        $html = ipView('view/navbar.php', $data)->render();
        return $html;
    }

    public static function ipInit()
    {
        $relativePath = ipRequest()->getRelativePath();
        $request = \Ip\ServiceLocator::request();

        if (ipIsManagementState() || !empty($_GET['aa']) || !empty($_GET['admin'])) {
            $sessionLifetime = ini_get('session.gc_maxlifetime');
            if (!$sessionLifetime) {
                $sessionLifetime = 120;
            }
            ipAddJsVariable('ipAdminSessionRefresh', $sessionLifetime - 10);
        }

        $getVariables = ipRequest()->getRequest();
        if (isset($getVariables['safemode'])) {
            $getVariables['safeMode'] = $getVariables['safemode'];
        }
        if (isset($getVariables['safeMode']) && \Ip\Internal\Admin\Backend::userId()) {
            Model::setSafeMode($getVariables['safeMode']);
        }
    }

    public static function ipAdminLoginFailed($data)
    {
        $securityModel = SecurityModel::instance();
        $securityModel->registerFailedLogin($data['username'], $data['ip']);
    }

    public static function ipCronExecute($data)
    {
        if ($data['firstTimeThisDay']) {
            $securityModel = SecurityModel::instance();
            $securityModel->cleanup();
        }
    }

}
