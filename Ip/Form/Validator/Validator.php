<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Validator;


//TODOX refactor to interface
abstract class Validator{
    
    /**
     * 
     * Return false on success or string on error
     * Add extra params at the end if you need
     * 
     * @param array $values
     * @param string $valueKey - key value to be validated
     * @param string $environment - \Ip\Form::ENVIRONMENT_ADMIN or \Ip\Fomr::ENVIRONEMNT_PUBLIC
     */

    //TODOX rename to hasError, getError
    public abstract function validate($values, $valueKey, $environment);

    /**
     * Jquery tools compatible validation arguments
     */
    public function validatorAttributes() {
        return '';
    }
    
}