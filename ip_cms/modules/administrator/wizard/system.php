<?php
/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\administrator\wizard;

if (!defined('CMS')) exit;

class System{

    function init(){
        global $site;
        global $parametersMod;

        $showWizard = $parametersMod->getValue('administrator', 'wizard', 'options', 'show_wizard');
        if ($site->managementState() && $showWizard) {
            // loading required Javascript libraries
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-tools/jquery.tools.ui.tooltip.js',2);
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/wizard/public/jquery.simulate.js',2);
            // loading module's elements
            $site->addCSS(BASE_URL.MODULE_DIR.'administrator/wizard/public/wizard.css',2);
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/wizard/public/wizard.js',2);
        }
    }
}
