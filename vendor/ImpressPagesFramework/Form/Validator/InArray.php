<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;


class InArray extends \Ip\Form\Validator
{

    /**
     * Constructor
     *
     * @param array $data
     * @param string $errorMessage
     * @throws \Ip\Exception
     */
    public function __construct($data, $errorMessage = null)
    {
        if (!is_array($data)) {
            throw new \Ip\Exception('InArray validator expect array of strings');
        }
        parent::__construct($data, $errorMessage);
    }

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

        if (!in_array($values[$valueKey], $this->data)) {
            if ($this->errorMessage) {
                return $this->errorMessage;
            }
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = __('The value has to be one of:', 'Ip-admin');
            } else {
                $errorText = __('The value has to be one of:', 'Ip');
            }
            $errorText .= ' ' . implode(', ', $this->data);

            return $errorText;
        }

        return false;
    }

}
