<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Wizard;


class System{

    function init(){

        if (\Ip\ServiceLocator::getContent()->isManagementState()) {
            // loading required Javascript libraries
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/jquery-tools/jquery.tools.ui.tooltip.js'),2);
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/jquery-tools/jquery.tools.ui.overlay.js'),2);
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/jquery-tools/jquery.tools.toolbox.expose.js'),2);
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Wizard/public/jquery.simulate.js'),2);
            // loading module's elements
            ipAddCss(ipGetConfig()->coreModuleUrl('Wizard/public/wizard.css'),2);
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Wizard/public/wizard.js'),2);
        }
    }
}
