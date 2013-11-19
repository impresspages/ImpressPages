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

        if (\Ip\ServiceLocator::getContent()->isManagementState()) {
            ipAddCss(ipGetConfig()->coreModuleUrl('InlineManagement/public/inline_management.css'));

            ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));

            ipAddJavascript(ipGetConfig()->coreModuleUrl('InlineManagement/public/inlineManagementControls.js'));
            ipAddJavascript(ipGetConfig()->coreModuleUrl('InlineManagement/public/inlineManagementLogo.js'));
            ipAddJavascript(ipGetConfig()->coreModuleUrl('InlineManagement/public/inlineManagementString.js'));
            ipAddJavascript(ipGetConfig()->coreModuleUrl('InlineManagement/public/inlineManagementText.js'));
            ipAddJavascript(ipGetConfig()->coreModuleUrl('InlineManagement/public/inlineManagementImage.js'));

            $response = \Ip\ServiceLocator::getResponse();
            if (method_exists($response, 'addJavascriptContent')) {
                $response->addJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            }

            ipAddJavascript(ipGetConfig()->coreModuleUrl('InlineManagement/public/inlineManagement.js'));

            ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.full.js'));
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.browserplus.js'));
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.gears.js'));

            ipAddJavascript(ipGetConfig()->coreModuleUrl('Upload/assets/jquery.ip.uploadImage.js'));
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Upload/assets/jquery.ip.uploadFile.js'));

            ipAddJavascript(ipGetConfig()->coreModuleUrl('InlineManagement/public/jquery.fontselector.js'));

            ipAddCss(ipGetConfig()->coreModuleUrl('Assets/assets/js/jquery-colorpicker/colorpicker.css'));
            ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


