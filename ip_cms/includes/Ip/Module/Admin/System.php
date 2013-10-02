<?php
namespace Ip\Module\Admin;


class System {

    public function init()
    {
        $dispatcher = \Ip\ServiceLocator::getDispatcher();
        $dispatcher->bind('site.afterInit', array($this, 'afterInit'));
    }

    public function afterInit()
    {
        $site = \Ip\ServiceLocator::getSite();
        $config = \Ip\ServiceLocator::getConfig();

        if ($site->managementState() || !empty($_SESSION['backend_session']['user_id'])) {
            $site->addJavascriptContent('bodyMargin', "window.document.body.style.marginTop = '60px';", -1);

            $site->addCss($config->getCoreModuleUrl().'Admin/Public/admin.css');

            //add tool bar
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
            $site->addJavascript($config->getCoreModuleUrl().'Admin/Public/admin.js');
//            $toolbarHtml = \Ip\View::create('View/Toolbar.php')->render();
//            $site->addJavascriptVariable('ipModuleAdminToolbarHtml', $toolbarHtml);

            //add navigation bar
            $data = array(
                'menuItems' => Model::instance()->getAdminMenuItems()
            );
            $navigationHtml = \Ip\View::create('View/Navigation.php', $data)->render();
            $site->addJavascriptVariable('ipModuleAdminNavigationHtml', $navigationHtml);
        }

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
        $data = array(
            'menuItems' => Model::instance()->getAdminMenuItems()
        );
        $navigationHtml = \Ip\View::create('View/Navigation.php', $data)->render();

        $config = \Ip\ServiceLocator::getConfig();

        $code = '    <link href="' . $config->getCoreModuleUrl() . 'Admin/Public/admin.css" type="text/css" rel="stylesheet" media="screen" />' . "\n";
        $code.= "    <script>window.jQuery || document.write('<script src=\"" . BASE_URL . LIBRARY_DIR . "js/jquery/jquery.js\"><\\/script>');</script>\n";
        $code.= '    <script type="text/javascript" src="' . $config->getCoreModuleUrl() . 'Admin/Public/admin.js" ></script>' . "\n";
        $code .= '   <script type="text/javascript"> var ipModuleAdminNavigationHtml = ' . json_encode($navigationHtml) . ';</script>' . "\n";
        $newHtml = preg_replace('%</head>%i', $code . '</head>', $html, 1);

        if ($newHtml == $html) {
            // tag not found
        }

        $newHtml = preg_replace("%(<body.*?>)%is", "$1\n<script>window.document.body.style.marginTop = '60px';</script>\n", $newHtml);

        return $newHtml;
    }

}
