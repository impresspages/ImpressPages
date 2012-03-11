<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\IpForm\Validator;



abstract class Validator{
    
    /**
     * 
     * Return true if validation passed
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