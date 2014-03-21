<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;



use Ip\Form\Validator;

class Email extends Validator {

    public function getError($values, $valueKey, $environment) {
        if (empty($values[$valueKey])) {
            return false;
        }
        $value = $values[$valueKey];

        if (!preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $value)) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Please enter a valid email address.', 'Ip-admin');
            } else {
                $errorText = __('Please enter a valid email address.', 'Ip');
            }
            return $errorText;
        } else {
            return false;
        }
    }


    public function validatorAttributes() {
        return 'type="email"';
    }

}
