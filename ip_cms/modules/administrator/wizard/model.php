<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\wizard;

if (!defined('CMS')) exit;

class Model{

    public static function disableWizard() {
        global $parametersMod;
        $parametersMod->setValue('administrator', 'wizard', 'options', 'show_wizard', false);
        return true;
    }

}
