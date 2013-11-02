<?php
namespace Ip\Module\Admin;


class System {

    protected static $disablePanel = false;

    public function init()
    {
        $dispatcher = \Ip\ServiceLocator::getDispatcher();

        $dispatcher->bind('site.afterInit', array($this, 'initAdmin'));
        $dispatcher->bind('site.beforeError404', array($this, 'catchAdminUrls'));

        $site = \Ip\ServiceLocator::getSite();
        if ($site->managementState() || !empty($_GET['m']) && !empty($_GET['g']) && !empty($_GET['aa']) || !empty($_GET['admin'])) {
            $sessionLifetime = ini_get('session.gc_maxlifetime');
            if (!$sessionLifetime) {
                $sessionLifetime = 120;
            }
            $site->addJavascriptVariable('ipAdminSessionRefresh', $sessionLifetime - 10);
        }

        $getVariables = \Ip\Request::getRequest();
        if (isset($getVariables['safemode']) && \Ip\Backend::userId()) {
            Model::setSafeMode($getVariables['safemode']);
        }
    }

    public function catchAdminUrls(\Ip\Event $event)
    {
        $relativePath = \Ip\Request::getRelativePath();

        if (in_array($relativePath, array('admin', 'admin/', 'admin.php', 'admin.php/'))) {
            $event->addProcessed();
            self::$disablePanel = true;
            $controller = new \Ip\Module\Admin\SiteController();
            $controller->login();
        }

        if ('ip_backend_frames.php' == $relativePath) {
            header('Location: ' . \Ip\Config::baseUrl('admin'));
            exit();
        }
    }

    public function initAdmin()
    {
        $site = \Ip\ServiceLocator::getSite();

        if (!self::$disablePanel && ($site->managementState() || !empty($_GET['aa']) ) && !empty($_SESSION['backend_session']['user_id'])) {
            $site->addCss(\Ip\Config::coreModuleUrl('Admin/Public/admin.css'));

            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery/jquery.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('Admin/Public/admin.js'));

            $site->addJavascriptVariable('ipAdminToolbar', $this->getAdminToolbarHtml());
        }

    }


    protected function getAdminToolbarHtml()
    {
        $requestData = \Ip\ServiceLocator::getRequest()->getRequest();
        $curModTitle = '';
        $curModUrl = '';
        $helpUrl = 'http://www.impresspages.org/help2';

        if (!empty($requestData['module_id']) && !empty($requestData['module_id'])){
            $curModule = \Ip\Deprecated\Db::getModule($requestData['module_id']);
        } elseif (!empty($requestData['cms_action']) && $requestData['cms_action'] == 'manage') {
            $curModule = \Ip\Deprecated\Db::getModule(null, 'standard', 'content_management');
        } elseif (!empty($_GET['m']) && !empty($_GET['g'])) {
            $curModule = \Ip\Deprecated\Db::getModule(null, $_GET['g'], $_GET['m']);
        }

        if (isset($curModule) && $curModule) {
            $helpUrl = 'http://www.impresspages.org/help2/' . $curModule['m_name'];
            $curModTitle = $curModule['m_translation'];
            $curModUrl = \Ip\Config::baseUrl('?admin=1&module_id=' . $curModule['id'] . '&security_token=' . \Ip\ServiceLocator::getSession()->getSecurityToken());
        }



        $data = array(
            'menuItems' => Model::instance()->getAdminMenuItems(),
            'curModTitle' => $curModTitle,
            'curModUrl' => $curModUrl,
            'helpUrl' => $helpUrl
        );
        $html = \Ip\View::create('View/toolbar.php', $data)->render();
        return $html;
    }


    /**
     * Injects admin html into old backend modules.
     *
     * @deprecated
     * @param string $html
     * @return mixed
     */
    public function injectAdminHtml($html)
    {
        $toolbarHtml = $this->getAdminToolbarHtml();

        $code = '    <link href="' . \Ip\Config::coreModuleUrl('Admin/Public/admin.css') . '" type="text/css" rel="stylesheet" media="screen" />' . "\n";
        $code .= '    <link href="' . \Ip\Config::libraryUrl('fonts/font-awesome/font-awesome.css') . '" type="text/css" rel="stylesheet" media="screen" />' . "\n";
        $code .= "   <script>window.jQuery || document.write('<script src=\"" . \Ip\Config::libraryUrl('js/jquery/jquery.js') . "\"><\\/script>');</script>\n";
        $code .= '   <script type="text/javascript"> var ipAdminToolbar = ' . json_encode($toolbarHtml) . ';</script>' . "\n";
        $code .= '   <script type="text/javascript" src="' . $config->coreModuleUrl() . 'Admin/Public/admin.js" ></script>' . "\n";
        $newHtml = preg_replace('%</head>%i', $code . '</head>', $html, 1);

        if ($newHtml == $html) {
            // tag not found
        }

        /*$newHtml = preg_replace("%(<body.*?>)%is", "$1\n<script>window.document.body.style.marginTop = '30px';</script>\n", $newHtml);*/

        return $newHtml;
    }

}
