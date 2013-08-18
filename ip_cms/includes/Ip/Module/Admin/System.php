<?php
namespace Ip\Module\Admin;


class System{

    public function init()
    {
        $site = \Ip\ServiceLocator::getSite();
        $config = \Ip\ServiceLocator::getConfig();

        if ($site->managementState()) {
            $site->addCss($config->getCoreModuleUrl().'Admin/Public/admin.css');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
            $site->addJavascript($config->getCoreModuleUrl().'Admin/Public/admin.js');
            $panelHtml = \Ip\View::create('View/AdminToolbar.php')->render();
            $site->addJavascriptVariable('ipModuleAdminPanelHtml', $panelHtml);
        }
    }
}
