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
class TransformException extends \Exception
{
    //error codes
    const UNKNOWN_MIME_TYPE  = 1;
    const TOO_BIG_IMAGE = 2;
    const WRITE_PERMISSION = 3;
    const MISSING_FILE = 4;


    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }


}
