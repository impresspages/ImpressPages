<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;


class Regex extends \Ip\Form\Validator
{

    /**
     * Get error
     *
     * @param array $values
     * @param int $valueKey
     * @param $environment
     * @return string|bool
     */
    public function getError($values, $valueKey, $environment)
    {
        if (empty($values[$valueKey])) {
            return false;
        }

        if (!preg_match($this->data, $values[$valueKey])) {
            if ($this->errorMessage) {
                return $this->errorMessage;
            }
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('Please correct this value', 'Ip-admin');
            } else {
                $errorText = __('Please correct this value', 'Ip');
            }
            return $errorText;
        }

        return false;
    }

}
