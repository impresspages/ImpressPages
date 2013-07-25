<?php
/**
 * @package ImpressPages

 *
 */


namespace IpUpdate\Gui;


class Exception extends \Exception
{
    //error codes
    const OTHER = 0;
    const VIEW = 1;
    
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}