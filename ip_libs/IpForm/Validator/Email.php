<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Validator;



class Email extends Validator {
    
    public function validate($value) {
        if (empty($value)) {
            return false;
        }
        if (!preg_match('#^A[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $value)) {
            return 'Invalid email';
        } else {
            return false;
        }
    }
    
}