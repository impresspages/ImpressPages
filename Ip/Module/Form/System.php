<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Form;


class System
{
    public function init()
    {
        $site = \Ip\ServiceLocator::getSite();
        $site->addJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
        $site->addJavascript(\Ip\Config::coreModuleUrl('Form/assets/form.js'));
    }
}