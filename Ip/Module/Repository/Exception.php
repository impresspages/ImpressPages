<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip\Module\Repository;



/**
 * IpCmsException class
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