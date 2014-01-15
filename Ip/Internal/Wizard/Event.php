<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Wizard;


class Event
{

    public static function ipInit()
    {
        if (ipIsManagementState()) {
            // loading required Javascript libraries
            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-tools/jquery.tools.ui.tooltip.js'), null, 20);
            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-tools/jquery.tools.ui.overlay.js'), null, 20);
            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-tools/jquery.tools.toolbox.expose.js'), null, 20);
            ipAddJs(ipFileUrl('Ip/Internal/Wizard/assets/jquery.simulate.js'), null, 20);
            // loading module's elements
            ipAddCss(ipFileUrl('Ip/Internal/Wizard/assets/wizard.css'), null, 20);
            ipAddJs(ipFileUrl('Ip/Internal/Wizard/assets/wizard.js'), null, 20);
        }
    }
}
