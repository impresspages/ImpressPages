<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip;


/**
 * IpCmsException class
 */
class Exception extends \Exception
{
    //error codes
    const DB = 0;
    const VIEW = 1;
    const EVENT = 2;
    const REVISION = 3;
    const WIDGET = 4;
    const SECURITY = 5;
    const SYSTEM_VARIABLE = 6;
    const ECOMMERCE = 7;
    const PLUGIN_SETUP = 8;
    const OTHER = 999;
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 999, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}