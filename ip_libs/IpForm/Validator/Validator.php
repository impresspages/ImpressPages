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
     * @param mixed $value
     */
    public abstract function validate($value);

    /**
     * Jquery tools compatible validation arguments
     */
    public function jtoolsArgs() {
        return '';
    }
    
}