<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;


class InArray extends \Ip\Form\Validator {

    public function __construct($data, $errorMessage = null)
    {
        if (!is_array($data)) {
            throw \Ip\Exception("InArray validator expect array of strings");
        }
        parent::__construct($data, $errorMessage);
    }

    public function getError($values, $valueKey, $environment) {
        if (empty($values[$valueKey])) {
            return FALSE;
        }

        if (!in_array($values[$valueKey], $this->data)) {
            if ($this->errorMessage) {
                return $this->errorMessage;
            }
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('The value has to be one of:', 'ipAdmin');
            } else {
                $errorText = __('The value has to be one of:', 'ipPublic');
            }
            $errorText . ' ' . implode(', ', $this->values);
            return $errorText;
        }

        return FALSE;
    }

}
