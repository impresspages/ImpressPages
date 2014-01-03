<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;



class Email extends Validator {
    
    public function getError($values, $valueKey, $environment) {
        if (empty($values[$valueKey])) {
            return false;
        }
        $value = $values[$valueKey];
        
        if (!preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $value)) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Please enter a valid email address.', 'ipAdmin');
            } else {
                $errorText = __('Please enter a valid email address.', 'ipPublic');
            }
            return $errorText;
        } else {
            return false;
        }
    }
    
}