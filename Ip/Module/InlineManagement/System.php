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
            ipAddCss(ipUrl('Ip/Module/InlineManagement/assets/inline_management.css'));

            ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/jquery.js'));

            ipAddJavascript(ipUrl('Ip/Module/InlineManagement/assets/inlineManagementControls.js'));
            ipAddJavascript(ipUrl('Ip/Module/InlineManagement/assets/inlineManagementLogo.js'));
            ipAddJavascript(ipUrl('Ip/Module/InlineManagement/assets/inlineManagementString.js'));
            ipAddJavascript(ipUrl('Ip/Module/InlineManagement/assets/inlineManagementText.js'));
            ipAddJavascript(ipUrl('Ip/Module/InlineManagement/assets/inlineManagementImage.js'));

            $response = \Ip\ServiceLocator::response();
            if (method_exists($response, 'addJavascriptContent')) {
                $response->addJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            }

            ipAddJavascript(ipUrl('Ip/Module/InlineManagement/assets/inlineManagement.js'));

            ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/plupload/plupload.full.js'));
            ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/plupload/plupload.browserplus.js'));
            ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/plupload/plupload.gears.js'));

            ipAddJavascript(ipUrl('Ip/Module/Upload/assets/jquery.ip.uploadImage.js'));
            ipAddJavascript(ipUrl('Ip/Module/Upload/assets/jquery.ip.uploadFile.js'));

            ipAddJavascript(ipUrl('Ip/Module/InlineManagement/assets/jquery.fontselector.js'));

            ipAddCss(ipUrl('Ip/Module/Assets/assets/js/jquery-colorpicker/colorpicker.css'));
            ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


