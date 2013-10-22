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
            $site->addCss(BASE_URL.MODULE_DIR.'developer/inline_management/public/inline_management.css');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');

            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/inline_management/public/inlineManagementControls.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/inline_management/public/inlineManagementLogo.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/inline_management/public/inlineManagementString.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/inline_management/public/inlineManagementText.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/inline_management/public/inlineManagementImage.js');
            $site->addJavascriptContent('controls', \Ip\View::create('view/management/controls.php')->render());
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/inline_management/public/inlineManagement.js');

            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.full.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.browserplus.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/plupload/plupload.gears.js');

            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/upload/jquery.ip.uploadImage.js?v=1');
            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/upload/jquery.ip.uploadFile.js?v=1');

            $site->addJavascript(BASE_URL.MODULE_DIR.'developer/inline_management/public/jquery.fontselector.js');

            $site->addCSS(BASE_URL.LIBRARY_DIR.'js/jquery-colorpicker/colorpicker.css');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-colorpicker/colorpicker.js');
        }
    }
}


