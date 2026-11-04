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

        if (!empty($requestData['aa'])) {
            $parts = explode('.', $requestData['aa']);
            $curModule = $parts[0];
        } else {
            $curModule = "Content";
        }

        if (isset($curModule) && $curModule) {
            $title = $curModule;
            if ($curModule == 'Email') {
                $title = 'Email log'; // same name as in the System menu and on the page itself
            }
            $pluginConfig = \Ip\Internal\Plugins\Service::getPluginConfig($curModule);
            
            $curModTitle = __($title, 'Ip-admin', false);
            $curModUrl = ipActionUrl(array('aa' => $curModule . '.index'));
            $curModIcon = Model::getAdminMenuItemIcon($curModule);
            
            // Plugins: title and icon from their plugin.json. Core modules keep the translated title above,
            // because getPluginConfig() falls back to the bare module name for them.
            if (!in_array($curModule, \Ip\Internal\Plugins\Model::getModules())) {
                $curModTitle = isset($pluginConfig['title']) ? __($pluginConfig['title'], $curModule, false) : $curModTitle;
                $curModIcon = isset($pluginConfig['icon']) ? $pluginConfig['icon'] : $curModIcon;
            }
        }

        $navbarButtons = array(
            array(
                'text' => esc(__('Log out', 'Ip-admin', false)),
                'hint' => __('Log out', 'Ip-admin', false),
                'url' => ipActionUrl(array('sa' => 'Admin.logout')),
                'class' => 'ipsAdminLogout',
                'faIcon' => 'fa-power-off'
            )
        );

        $navbarButtons = ipFilter('ipAdminNavbarButtons', $navbarButtons);

        $navbarCenterElements = ipFilter('ipAdminNavbarCenterElements', []);

        $data = array(
            'menuItems' => Model::instance()->getAdminMenuItems($curModule),
            'curModTitle' => $curModTitle,
            'curModUrl' => $curModUrl,
            'curModIcon' => $curModIcon,
            'navbarButtons' => array_reverse($navbarButtons),
            'navbarCenterElements' => $navbarCenterElements
        );


        $html = ipView('view/navbar.php', $data)->render();
        return $html;
    }

    public static function ipInitFinished ()
    {
        $request = \Ip\ServiceLocator::request();
        $safeMode = $request->getQuery('safeMode');
        if ($safeMode === null) {
            $safeMode = $request->getQuery('safemode');
        }

        if ($safeMode !== null && \Ip\Internal\Admin\Backend::userId()) {
            Model::setSafeMode($safeMode);
        }
    }

    public static function ipBeforeController()
    {


        // Admin screens: icon rail (level 1) and section column (level 2), see Navigation
        if (ipRoute()->isAdmin()) {
            ipAddJs('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.js');
            ipAddCss('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.css');

            if (ipAdminId()) {
                $groups = Navigation::groups(Navigation::currentModule());
                ipResponse()->setLayoutVariable('adminRail', ipView('view/rail.php', array(
                    'groups' => $groups,
                    'siteName' => ipGetOptionLang('Config.websiteTitle', null, 'ImpressPages'),
                ))->render());
                $currentGroup = Navigation::currentGroup($groups);
                if ($currentGroup) {
                    // browser tab title, e.g. "General · Settings"
                    $screenTitle = $currentGroup['title'];
                    foreach ($currentGroup['items'] as $item) {
                        if ($item['current'] && $item['title'] != $currentGroup['title']) {
                            $screenTitle = $item['title'] . ' · ' . $currentGroup['title'];
                        }
                    }
                    ipResponse()->setLayoutVariable('adminScreenTitle', $screenTitle);
                }
                if ($currentGroup && count($currentGroup['items']) > 1) {
                    ipResponse()->setLayoutVariable('adminSection', ipView('view/section.php', array('group' => $currentGroup))->render());
                }
            }
        }

        if (ipAdminId() && !ipRequest()->getRequest('pa') || ipRequest()->getRequest('aa') && ipAdminId()) {
            if (!ipRequest()->getQuery('ipDesignPreview') && !ipRequest()->getQuery('disableAdminNavbar')) {
                ipAddJs('Ip/Internal/Admin/assets/admin.js');
                if (!ipRoute()->isAdmin()) {
                    // page editor on the site: a slim bar with publishing tools and a way back to the admin
                    ipAddJsVariable('ipAdminNavbar', static::getAdminNavbarHtml());
                }
            }
        }

        // Show popup with autogenerated user information if needed
        $adminIsAutogenerated = ipStorage()->get('Ip', 'adminIsAutogenerated');
        if ($adminIsAutogenerated) {
            $adminId = \Ip\Internal\Admin\Backend::userId();
            $admin = \Ip\Internal\Administrators\Model::getById($adminId);
            ipAddJs('Ip/Internal/Admin/assets/adminIsAutogenerated.js');
            $data = array(
                'adminUsername' => $admin['username'],
                'adminPassword' => ipStorage()->get('Ip', 'adminIsAutogenerated'),
                'adminEmail' => $admin['email']
            );
            ipAddJsVariable('ipAdminIsAutogenerated', ipView('view/adminIsAutoGenerated.php', $data)->render());
        }


        if (ipContent()->getCurrentPage()) {
            // initialize management
            if (ipIsManagementState()) {
                if (!ipRequest()->getQuery('ipDesignPreview') && !ipRequest()->getQuery('disableManagement')) {
                    \Ip\Internal\Content\Helper::initManagement();
                }
            }

            //show page content
            $response = ipResponse();
            $response->setDescription(\Ip\ServiceLocator::content()->getDescription());
            $response->setKeywords(ipContent()->getKeywords());
            $response->setTitle(ipContent()->getTitle());

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
