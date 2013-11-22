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
            ipAddCss(ipConfig()->coreModuleUrl('InlineManagement/assets/inline_management.css'));

            ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));

            ipAddJavascript(ipConfig()->coreModuleUrl('InlineManagement/assets/inlineManagementControls.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('InlineManagement/assets/inlineManagementLogo.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('InlineManagement/assets/inlineManagementString.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('InlineManagement/assets/inlineManagementText.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('InlineManagement/assets/inlineManagementImage.js'));

            $response = \Ip\ServiceLocator::response();
            if (method_exists($response, 'addJavascriptContent')) {
                $response->addJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            }

            ipAddJavascript(ipConfig()->coreModuleUrl('InlineManagement/assets/inlineManagement.js'));

            ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.full.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.browserplus.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.gears.js'));

            ipAddJavascript(ipConfig()->coreModuleUrl('Upload/assets/jquery.ip.uploadImage.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Upload/assets/jquery.ip.uploadFile.js'));

            ipAddJavascript(ipConfig()->coreModuleUrl('InlineManagement/assets/jquery.fontselector.js'));

            ipAddCss(ipConfig()->coreModuleUrl('Assets/assets/js/jquery-colorpicker/colorpicker.css'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


