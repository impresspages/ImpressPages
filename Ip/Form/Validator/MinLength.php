<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;

use Ip\Form\Validator;


class MinLength extends Validator
{
    protected $minLength;

    public function __construct($data = array(), $errorMessage = null)
    {
        $this->minLength = (int)$data;
        $this->errorMessage = $errorMessage;
        if (!is_numeric($data)) {
            throw new \Ip\Exception('minLength validator expect integer number as a first parameter');
        }
        parent::__construct($data, $errorMessage);
    }

    /**
     * Get error
     *
     * @param array $values all submitted data
     * @param int $valueKey key of value to be validated
     * @param $environment
     * @return string|bool
     */
    public function getError($values, $valueKey, $environment)
    {
        if (!empty($values[$valueKey]) && mb_strlen($values[$valueKey]) < $this->minLength ) {
            if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
                $errorText = sprintf(__('Min %d characters', 'Ip-admin'), $this->minLength);
            } else {
                $errorText = sprintf(__('Min %d character', 'Ip'), $this->minLength);;
            }

            return $errorText;
        } else {
            return false;
        }
    }

    /**
     * Validator attributes
     *
     * @return string
     */
    public function validatorAttributes()
    {
        return 'pattern="(.+){' . $this->minLength . ',}"';
    }
}
