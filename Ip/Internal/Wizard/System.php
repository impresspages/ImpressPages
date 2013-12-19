<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Wizard;


class System{

    function init(){

        if (\Ip\ServiceLocator::content()->isManagementState()) {
            // loading required Javascript libraries
            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-tools/jquery.tools.ui.tooltip.js'),2);
            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-tools/jquery.tools.ui.overlay.js'),2);
            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-tools/jquery.tools.toolbox.expose.js'),2);
            ipAddJavascript(ipFileUrl('Ip/Internal/Wizard/assets/jquery.simulate.js'),2);
            // loading module's elements
            ipAddCss(ipFileUrl('Ip/Internal/Wizard/assets/wizard.css'),2);
            ipAddJavascript(ipFileUrl('Ip/Internal/Wizard/assets/wizard.js'),2);
        }
    }
}
