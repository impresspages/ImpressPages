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
            ipAddCss(\Ip\Config::coreModuleUrl('InlineManagement/public/inline_management.css'));

            ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));

            ipAddJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementControls.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementLogo.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementString.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementText.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementImage.js'));
            ipAddJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            ipAddJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagement.js'));

            ipAddJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.full.js'));
            ipAddJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.browserplus.js'));
            ipAddJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.gears.js'));

            ipAddJavascript(\Ip\Config::coreModuleUrl('Upload/assets/jquery.ip.uploadImage.js'));
            ipAddJavascript(\Ip\Config::coreModuleUrl('Upload/assets/jquery.ip.uploadFile.js'));

            ipAddJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/jquery.fontselector.js'));

            ipAddCss(\Ip\Config::libraryUrl('js/jquery-colorpicker/colorpicker.css'));
            ipAddJavascript(\Ip\Config::libraryUrl('js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


