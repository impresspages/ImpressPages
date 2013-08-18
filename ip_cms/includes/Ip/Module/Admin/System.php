<?php
namespace Ip\Module\Admin;


class System{

    public function init()
    {
        $site = \Ip\ServiceLocator::getSite();
        $config = \Ip\ServiceLocator::getConfig();

        if ($site->managementState()) {
            $site->addCss($config->getCoreModuleUrl().'Admin/public/admin.css');
            $site->addJavascript($config->getCoreModuleUrl().'Admin/public/admin.js');
        }
    }
}
