<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\InlineManagement;


class System
{
    function init()
    {

        if (\Ip\ServiceLocator::content()->isManagementState()) {
            ipAddCss(ipFileUrl('Ip/Module/InlineManagement/assets/inline_management.css'));


            if (ipConfig()->getRaw('DEBUG_MODE')) {
                ipAddJavascript(ipFileUrl('Ip/Module/InlineManagement/assets/src/inlineManagement.js'));
                ipAddJavascript(ipFileUrl('Ip/Module/InlineManagement/assets/src/inlineManagementControls.js'));
                ipAddJavascript(ipFileUrl('Ip/Module/InlineManagement/assets/src/inlineManagementImage.js'));
                ipAddJavascript(ipFileUrl('Ip/Module/InlineManagement/assets/src/inlineManagementLogo.js'));
                ipAddJavascript(ipFileUrl('Ip/Module/InlineManagement/assets/src/inlineManagementString.js'));
                ipAddJavascript(ipFileUrl('Ip/Module/InlineManagement/assets/src/inlineManagementText.js'));
                ipAddJavascript(ipFileUrl('Ip/Module/InlineManagement/assets/src/jquery.fontselector.js'));
            } else {
                ipAddJavascript(ipFileUrl('Ip/Module/InlineManagement/assets/inlineManagement.min.js'));
            }

            $response = \Ip\ServiceLocator::response();
            if (method_exists($response, 'addJavascriptContent')) {
                $response->addJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            }


            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/plupload/plupload.full.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/plupload/plupload.browserplus.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/plupload/plupload.gears.js'));

            ipAddJavascript(ipFileUrl('Ip/Module/Upload/assets/jquery.ip.uploadImage.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Upload/assets/jquery.ip.uploadFile.js'));

            ipAddCss(ipFileUrl('Ip/Module/Ip/assets/js/jquery-colorpicker/colorpicker.css'));
            ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


