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
        ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
        ipAddJavascript(\Ip\Config::coreModuleUrl('Form/assets/form.js'));
    }
}