<?php
namespace Ip\Module\Admin;


class System {

    protected static $disablePanel = false;

    public function init()
    {
        $relativePath = \Ip\Request::getRelativePath();
        $request = \Ip\ServiceLocator::getRequest();

        if (in_array($relativePath, array('admin', 'admin/', 'admin.php', 'admin.php/')) && $request->isDefaultAction()) {
            \Ip\ServiceLocator::getResponse()->setLayout(\Ip\Config::coreModuleFile('/Admin/View/layout.php'));
            $request->setAction('Admin', 'login', \Ip\Internal\Request::CONTROLLER_TYPE_SITE);
        }


        $dispatcher = \Ip\ServiceLocator::getDispatcher();

        $dispatcher->bind('site.afterInit', array($this, 'initAdmin'));

        if (\Ip\ServiceLocator::getContent()->isManagementState() || !empty($_GET['aa']) || !empty($_GET['admin'])) {
            $sessionLifetime = ini_get('session.gc_maxlifetime');
            if (!$sessionLifetime) {
                $sessionLifetime = 120;
            }
            ipAddJavascriptVariable('ipAdminSessionRefresh', $sessionLifetime - 10);
        }

        $getVariables = \Ip\Request::getRequest();
        if (isset($getVariables['safemode']) && \Ip\Module\Admin\Backend::userId()) {
            Model::setSafeMode($getVariables['safemode']);
        }
    }


    public function initAdmin()
    {

        if (!self::$disablePanel && (\Ip\ServiceLocator::getContent()->isManagementState() || !empty($_GET['aa']) ) && !empty($_SESSION['backend_session']['userId'])) {
            ipAddCss(\Ip\Config::coreModuleUrl('Admin/Public/admin.css'));

            ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Admin/Public/admin.js'));

            ipAddJavascriptVariable('ipAdminToolbar', $this->getAdminToolbarHtml());
        }

    }


    protected function getAdminToolbarHtml()
    {
        $requestData = \Ip\ServiceLocator::getRequest()->getRequest();
        $curModTitle = '';
        $curModUrl = '';
        $helpUrl = 'http://www.impresspages.org/help2';

        if (!empty($requestData['aa'])) {
            $parts = explode('.', $requestData['aa']);
            $curModule = $parts[0];
        } elseif (!empty($requestData['cms_action']) && $requestData['cms_action'] == 'manage') {
            $curModule = "Content";
        }

        if (isset($curModule) && $curModule) {
            $helpUrl = 'http://www.impresspages.org/help2/' . $curModule;
            $curModTitle = $curModule; //TODOX translation
            $curModUrl = \Ip\Config::baseUrl('', array('aa' => $curModule . '.index'));
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
        $code .= "   <script>window.jQuery || document.write('<script src=\"" . \Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js') . "\"><\\/script>');</script>\n";
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
