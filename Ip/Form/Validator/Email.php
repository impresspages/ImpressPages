<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;



class Email extends Validator {
    
    public function validate($values, $valueKey) {
        if (empty($values[$valueKey])) {
            return false;
        }
        $value = $values[$valueKey];
        
        if (!preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $value)) {
            $parametersMod = \Ip\ServiceLocator::getParametersMod();
            return $parametersMod->getValue("developer", "form", "error_messages", "email");
        } else {
            return false;
        }
    }
    
}