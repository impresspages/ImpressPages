<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;



use Ip\Form\Validator;

class Required extends Validator {

    public function getError($values, $valueKey, $environment) {
        if (!array_key_exists($valueKey, $values) || in_array($values[$valueKey], array(null, false, '', array()), true)) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Required field', 'Ip-admin');
            } else {
                $errorText = __('Required field', 'Ip');
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
