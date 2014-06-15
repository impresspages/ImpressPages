<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\InlineManagement;


class Event
{
    public static function ipBeforeController()
    {

        if (ipIsManagementState()) {
            if (ipConfig()->isDebugMode()) {
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagement.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagementControls.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagementImage.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagementLogo.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/inlineManagementText.js');
                ipAddJs('Ip/Internal/InlineManagement/assets/src/jquery.fontselector.js');
            } else {
                ipAddJs('Ip/Internal/InlineManagement/assets/inlineManagement.min.js');
            }

            ipAddJsVariable('ipModuleInlineManagementControls', ipView('view/management/controls.php')->render());

            ipAddJs('Ip/Internal/Content/assets/jquery.ip.uploadImage.js');

            ipAddCss('Ip/Internal/Core/assets/js/jquery-colorpicker/colorpicker.css');
            ipAddJs('Ip/Internal/Core/assets/js/jquery-colorpicker/colorpicker.js');
        }
    }
}


