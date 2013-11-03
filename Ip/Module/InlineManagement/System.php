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
        global $site;

        if ($site->managementState()) {
            $site->addCss(\Ip\Config::coreModuleUrl('InlineManagement/public/inline_management.css'));

            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery/jquery.js'));

            $site->addJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementControls.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementLogo.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementString.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementText.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagementImage.js'));
            $site->addJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            $site->addJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/inlineManagement.js'));

            $site->addJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.full.js'));
            $site->addJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.browserplus.js'));
            $site->addJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.gears.js'));

            $site->addJavascript(\Ip\Config::coreModuleUrl('Upload/assets/jquery.ip.uploadImage.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('Upload/assets/jquery.ip.uploadFile.js'));

            $site->addJavascript(\Ip\Config::coreModuleUrl('InlineManagement/public/jquery.fontselector.js'));

            $site->addCSS(\Ip\Config::libraryUrl('js/jquery-colorpicker/colorpicker.css'));
            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


