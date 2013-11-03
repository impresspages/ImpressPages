<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Wizard;


class System{

    function init(){
        $site = \Ip\ServiceLocator::getSite();

        if ($site->managementState()) {
            // loading required Javascript libraries
            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery-tools/jquery.tools.ui.tooltip.js'),2);
            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery-tools/jquery.tools.ui.overlay.js'),2);
            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery-tools/jquery.tools.toolbox.expose.js'),2);
            $site->addJavascript(\Ip\Config::coreModuleUrl('Wizard/public/jquery.simulate.js'),2);
            // loading module's elements
            $site->addCSS(\Ip\Config::coreModuleUrl('Wizard/public/wizard.css'),2);
            $site->addJavascript(\Ip\Config::coreModuleUrl('Wizard/public/wizard.js'),2);
        }
    }
}
