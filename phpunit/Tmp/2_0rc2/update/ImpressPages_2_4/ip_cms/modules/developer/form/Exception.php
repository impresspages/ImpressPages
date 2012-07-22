<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\form;


class Exception extends \Exception
{
    //error codes
    const INCORRECT_METHOD_TYPE = 0;
    const UNKNOWN_VALIDATOR = 1;
    

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    

}