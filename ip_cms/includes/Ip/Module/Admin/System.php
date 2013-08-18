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

        if ($site->managementState()) {
            $site->addCss($config->getCoreModuleUrl().'Admin/Public/admin.css');

            //add tool bar
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
            $site->addJavascript($config->getCoreModuleUrl().'Admin/Public/admin.js');
            $toolbarHtml = \Ip\View::create('View/Toolbar.php')->render();
            $site->addJavascriptVariable('ipModuleAdminToolbarHtml', $toolbarHtml);

            //add navigation bar
            $data = array(
                'menuItems' => Model::instance()->getAdminMenuItems()
            );
            $navigationHtml = \Ip\View::create('View/Navigation.php', $data)->render();
            $site->addJavascriptVariable('ipModuleAdminNavigationHtml', $navigationHtml);
        }

    }

}
