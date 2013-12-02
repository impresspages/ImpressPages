<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Wizard;


class System{

    function init(){

        if (\Ip\ServiceLocator::content()->isManagementState()) {
            // loading required Javascript libraries
            ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/jquery-tools/jquery.tools.ui.tooltip.js'),2);
            ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/jquery-tools/jquery.tools.ui.overlay.js'),2);
            ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/jquery-tools/jquery.tools.toolbox.expose.js'),2);
            ipAddJavascript(ipUrl('Ip/Module/Wizard/assets/jquery.simulate.js'),2);
            // loading module's elements
            ipAddCss(ipUrl('Ip/Module/Wizard/assets/wizard.css'),2);
            ipAddJavascript(ipUrl('Ip/Module/Wizard/assets/wizard.js'),2);
        }
    }
}
