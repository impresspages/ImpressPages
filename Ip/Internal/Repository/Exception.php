<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip\Internal\Repository;



/**
 * Exception class
 * @todo move to \Ip\Exception\Repository namespace
 */
class Exception extends \Exception
{
    //error codes
    const DB = 0;
    const SECURITY = 1;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    

}
