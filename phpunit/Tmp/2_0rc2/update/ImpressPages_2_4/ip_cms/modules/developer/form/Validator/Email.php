<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\form\Validator;



class Email extends Validator {
    
    public function validate($values, $valueKey) {
        if (empty($values[$valueKey])) {
            return false;
        }
        $value = $values[$valueKey];
        
        if (!preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $value)) {
            return 'Invalid email';
        } else {
            return false;
        }
    }
    
}