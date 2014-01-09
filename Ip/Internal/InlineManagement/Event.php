<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\InlineManagement;


class Event
{
    public static function ipInit()
    {

        if (ipIsManagementState()) {
            ipAddCss(ipFileUrl('Ip/Internal/InlineManagement/assets/inline_management.css'));


            if (ipConfig()->isDebugMode()) {
                ipAddJs(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagement.js'));
                ipAddJs(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementControls.js'));
                ipAddJs(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementImage.js'));
                ipAddJs(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementLogo.js'));
                ipAddJs(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementString.js'));
                ipAddJs(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementText.js'));
                ipAddJs(ipFileUrl('Ip/Internal/InlineManagement/assets/src/jquery.fontselector.js'));
            } else {
                ipAddJs(ipFileUrl('Ip/Internal/InlineManagement/assets/inlineManagement.min.js'));
            }

            $response = \Ip\ServiceLocator::response();
            if (method_exists($response, 'addJavascriptContent')) {
                $response->addJavascriptContent('controls', ipView('view/management/controls.php')->render());
            }


            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.full.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.browserplus.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.gears.js'));

            ipAddJs(ipFileUrl('Ip/Internal/Upload/assets/jquery.ip.uploadImage.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Upload/assets/jquery.ip.uploadFile.js'));

            ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-colorpicker/colorpicker.css'));
            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


