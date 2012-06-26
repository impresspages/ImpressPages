<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\wizard;

if (!defined('CMS')) exit;

class Model{

    public static function showWizard() {
        global $parametersMod;
        if (   $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_1')
            || $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_2')
            || $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_3')
            || $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_4')
            || $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_5')
            || $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_6') )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function disableWizard() {
        global $parametersMod;
        $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_1', false);
        $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_2', false);
        $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_3', false);
        $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_4', false);
        $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_5', false);
        $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_6', false);
        return true;
    }

    public static function disableWizardTip($id) {
        global $parametersMod;
        if (is_int((int)$id) && $id>=1 && $id<=6) {
            $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_'.(int)$id, false);
            return (int)$id;
        }
        return false;
    }

}
