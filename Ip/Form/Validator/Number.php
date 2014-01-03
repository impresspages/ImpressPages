<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;



class Number extends Validator {
    
    public function getError($values, $valueKey, $environment) {
        if (empty($values[$valueKey])) {
            return false;
        }
        $value = $values[$valueKey];
        if (!preg_match('/^[0-9]+$/', $value)) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Must be a number.', 'ipAdmin');
            } else {
                $errorText = __('Must be a number.', 'ipPublic');
            }
            return $errorText;
        } else {
            return false;
        }
    }
    
}