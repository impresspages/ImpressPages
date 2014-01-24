<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form;


abstract class Validator
{

    protected $data;
    protected $errorMessage;


    /**
     * All validators has to have the same constructor to make it easier to use them.
     * Thanks to this rule, you can add new validator to the form field without actually creating validator object, but just passing
     * validator class and data for the constructor. Eg.  $field->addValidator('validatorClass', $validationDAta);
     * @param array $data additional parameters to tune up the validator. Like regular expression for Regex validator
     * @param string $errorMessage override default error message
     */
    public function __construct($data = array(), $errorMessage = null)
    {
        $this->data = $data;
        $this->errorMessage = $errorMessage;
    }

    /**
     *
     * Return false on success or string on error
     * Add extra params at the end if you need
     *
     * @param array $values
     * @param string $valueKey - key value to be validated
     * @param string $environment - \Ip\Form::ENVIRONMENT_ADMIN or \Ip\Fomr::ENVIRONEMNT_PUBLIC
     */

    public abstract function getError($values, $valueKey, $environment);

    /**
     * Jquery tools compatible validation arguments
     */
    public function validatorAttributes() {
        return '';
    }

}
