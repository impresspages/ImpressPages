<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\InlineManagement;


class System
{
    function init()
    {

        if (\Ip\ServiceLocator::content()->isManagementState()) {
            ipAddCss(ipFileUrl('Ip/Internal/InlineManagement/assets/inline_management.css'));


            if (ipConfig()->isDebugMode()) {
                ipAddJavascript(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagement.js'));
                ipAddJavascript(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementControls.js'));
                ipAddJavascript(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementImage.js'));
                ipAddJavascript(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementLogo.js'));
                ipAddJavascript(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementString.js'));
                ipAddJavascript(ipFileUrl('Ip/Internal/InlineManagement/assets/src/inlineManagementText.js'));
                ipAddJavascript(ipFileUrl('Ip/Internal/InlineManagement/assets/src/jquery.fontselector.js'));
            } else {
                ipAddJavascript(ipFileUrl('Ip/Internal/InlineManagement/assets/inlineManagement.min.js'));
            }

            $response = \Ip\ServiceLocator::response();
            if (method_exists($response, 'addJavascriptContent')) {
                $response->addJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            }


            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.full.js'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.browserplus.js'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.gears.js'));

            ipAddJavascript(ipFileUrl('Ip/Internal/Upload/assets/jquery.ip.uploadImage.js'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Upload/assets/jquery.ip.uploadFile.js'));

            ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-colorpicker/colorpicker.css'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


