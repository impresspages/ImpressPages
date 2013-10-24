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
        if ($site->managementState()) {
            $sessionLifetime = ini_get('session.gc_maxlifetime');
            if (!$sessionLifetime) {
                $sessionLifetime = 120;
            }
            $site->addJavascriptVariable('ipAdminSessionRefresh', $sessionLifetime - 10);
        }

        $getVariables = \Ip\ServiceLocator::getRequest()->getRequest();
        if (isset($getVariables['safemode']) && \Ip\Backend::userId()) {
            Model::setSafeMode($getVariables['safemode']);
        }
    }

    public function catchAdminUrls(\Ip\Event $event)
    {
        $request = \Ip\ServiceLocator::getRequest();
        $relativePath = $request->getRelativePath();

        if (in_array($relativePath, array('admin', 'admin/', 'admin.php', 'admin.php/'))) {
            $event->addProcessed();
            self::$disablePanel = true;
            $controller = new \Ip\Module\Admin\SiteController();
            $controller->login();
        }

        if ('ip_backend_frames.php' == $relativePath) {
            header('Location: ' . BASE_URL . 'admin');
            exit();
        }
    }

    public function initAdmin()
    {
        $site = \Ip\ServiceLocator::getSite();
        $config = \Ip\ServiceLocator::getConfig();

        if (!self::$disablePanel && $site->managementState() && !empty($_SESSION['backend_session']['user_id'])) {
            $site->addCss($config->getCoreModuleUrl().'Admin/Public/admin.css');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
            $site->addJavascript($config->getCoreModuleUrl().'Admin/Public/admin.js');

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
            $curModule = \Db::getModule($requestData['module_id']);
        } elseif (!empty($requestData['cms_action']) && $requestData['cms_action'] == 'manage') {
            $curModule = \Db::getModule(null, 'standard', 'content_management');
        }

        if (isset($curModule) && $curModule) {
            $helpUrl = 'http://www.impresspages.org/help2/' . $curModule['m_name'];
            $curModTitle = $curModule['m_translation'];
            $curModUrl = BASE_URL . '?admin=1&module_id=' . $curModule['id'] . '&security_token=' . \Ip\ServiceLocator::getSession()->getSecurityToken();
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

        $config = \Ip\ServiceLocator::getConfig();

        $code = '    <link href="' . $config->getCoreModuleUrl() . 'Admin/Public/admin.css" type="text/css" rel="stylesheet" media="screen" />' . "\n";
        $code .= '    <link href="' . BASE_URL.LIBRARY_DIR . 'fonts/font-awesome/font-awesome.css" type="text/css" rel="stylesheet" media="screen" />' . "\n";
        $code .= "   <script>window.jQuery || document.write('<script src=\"" . BASE_URL . LIBRARY_DIR . "js/jquery/jquery.js\"><\\/script>');</script>\n";
        $code .= '   <script type="text/javascript"> var ipAdminToolbar = ' . json_encode($toolbarHtml) . ';</script>' . "\n";
        $code .= '   <script type="text/javascript" src="' . $config->getCoreModuleUrl() . 'Admin/Public/admin.js" ></script>' . "\n";
        $newHtml = preg_replace('%</head>%i', $code . '</head>', $html, 1);

        if ($newHtml == $html) {
            // tag not found
        }

        /*$newHtml = preg_replace("%(<body.*?>)%is", "$1\n<script>window.document.body.style.marginTop = '30px';</script>\n", $newHtml);*/

        return $newHtml;
    }

}
