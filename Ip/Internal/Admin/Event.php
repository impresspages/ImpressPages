<?php


namespace Ip\Internal\Admin;


class Event
{
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
        } else {
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

    public static function ipBeforeController()
    {
        $request = \Ip\ServiceLocator::request();

        if (ipIsManagementState() || $request->getQuery('aa') || $request->getQuery('admin')) {
            $sessionLifetime = ini_get('session.gc_maxlifetime');
            if (!$sessionLifetime) {
                $sessionLifetime = 120;
            }
            ipAddJsVariable('ipAdminSessionRefresh', $sessionLifetime - 10);
        }

        $safeMode = $request->getQuery('safeMode') || $request->getQuery('safemode');

        if ($safeMode !== null && \Ip\Internal\Admin\Backend::userId()) {
            Model::setSafeMode($safeMode);
        }

        //show admin submenu if needed
        if (ipRequest()->getControllerType() == \Ip\Request::CONTROLLER_TYPE_ADMIN) {
            $submenu = Submenu::getSubmenuItems();
            if ($submenu) {
                ipResponse()->setLayoutVariable('submenu', $submenu);
            }
        }

        // Show admin toolbar if admin is logged in:
        if (ipAdminId() && !ipRequest()->getRequest('pa') || ipRequest()->getRequest('aa') && ipAdminId()) {
            if (!ipRequest()->getQuery('ipDesignPreview') && !ipRequest()->getQuery('disableAdminBar')) {
                ipAddJs('Ip/Internal/Admin/assets/admin.js');
                ipAddJsVariable('ipAdminNavbar', static::getAdminNavbarHtml());
            }
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
