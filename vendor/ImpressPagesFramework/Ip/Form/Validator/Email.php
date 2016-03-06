<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;

use Ip\Form\Validator;


/**
 * Email field validator
 */
class Email extends Validator
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
        $value = $values[$valueKey];

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
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


}
