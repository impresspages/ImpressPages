<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Validator;



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