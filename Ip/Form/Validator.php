<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form;


/**
 * Form fields and administration grid data validation
 */
abstract class Validator
{

    protected $data;
    protected $errorMessage;

    /**
     * All validators has to have the same constructor to make it easier to use them
     *
     * Thanks to this rule, you can add new validator to the form field without actually creating validator object, but just passing
     * validator class and data for the constructor. Eg. $field->addValidator('validatorClass', $validationData);
     * @param array $data additional parameters to tune up the validator. Like regular expression for Regex validator.
     * @param string $errorMessage Override default error message.
     */
    public function __construct($data = array(), $errorMessage = null)
    {
        $this->data = $data;
        $this->errorMessage = $errorMessage;
    }

    /**
     *
     * Return false on success or string on error
     *
     * Add extra params at the end if you need.
     * @param array $values all submitted data
     * @param string $valueKey key of value to be validated
     * @param string $environment \Ip\Form::ENVIRONMENT_ADMIN or \Ip\Fomr::ENVIRONEMNT_PUBLIC
     */
    public abstract function getError($values, $valueKey, $environment);

    /**
     * jQuery tools compatible validation arguments
     *
     * @return string
     */
    public function validatorAttributes()
    {
        return '';
    }

}
