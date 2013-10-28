<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\developer\inline_management;


class System
{
    function init()
    {
        global $site;

        if ($site->managementState()) {
            $site->addCss(\Ip\Config::oldModuleUrl('developer/inline_management/public/inline_management.css'));

            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery/jquery.js'));

            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/inline_management/public/inlineManagementControls.js'));
            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/inline_management/public/inlineManagementLogo.js'));
            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/inline_management/public/inlineManagementString.js'));
            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/inline_management/public/inlineManagementText.js'));
            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/inline_management/public/inlineManagementImage.js'));
            $site->addJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/inline_management/public/inlineManagement.js'));

            $site->addJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.full.js'));
            $site->addJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.browserplus.js'));
            $site->addJavascript(\Ip\Config::libraryUrl('js/plupload/plupload.gears.js'));

            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/upload/jquery.ip.uploadImage.js?v=1'));
            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/upload/jquery.ip.uploadFile.js?v=1'));

            $site->addJavascript(\Ip\Config::oldModuleUrl('developer/inline_management/public/jquery.fontselector.js'));

            $site->addCSS(\Ip\Config::libraryUrl('js/jquery-colorpicker/colorpicker.css'));
            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery-colorpicker/colorpicker.js'));
        }
    }
}


