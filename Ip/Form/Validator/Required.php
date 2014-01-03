<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;



class Required extends Validator {

    public function getError($values, $valueKey, $environment) {
        if (!array_key_exists($valueKey, $values) || in_array($values[$valueKey], array(null, false, '', array()), true)) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Required field', 'ipAdmin');
            } else {
                $errorText = __('Required field', 'ipPublic');
            }

            return $errorText;
        } else {
            return false;
        }
    }

    public function validatorAttributes() {
        return 'required="required"';
    }

}
