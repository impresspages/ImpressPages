<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;


class Regex extends \Ip\Form\Validator {


    public function getError($values, $valueKey, $environment) {
        if (empty($values[$valueKey])) {
            return FALSE;
        }

        if (!preg_match($this->data, $values[$valueKey])) {
            if ($this->errorMessage) {
                return $this->errorMessage;
            }
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Please correct this value', 'ipAdmin');
            } else {
                $errorText = __('Please correct this value', 'ipPublic');
            }
            return $errorText;
        }

        return FALSE;
    }

}
