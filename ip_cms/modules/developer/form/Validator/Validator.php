<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 *
 */

namespace Modules\developer\form\Validator;



abstract class Validator{
    
    /**
     * 
     * Return false on success or string on error
     * Add extra params at the end if you need
     * 
     * @param array $values
     * @param string $valueKey - key value to be validated
     */
    public abstract function validate($values, $valueKey);

    /**
     * Jquery tools compatible validation arguments
     */
    public function jtoolsArgs() {
        return '';
    }
    
}