<?php
/**
 * @package ImpressPages

 *
 */

namespace Modules\developer\widgets;


if (!defined('CMS')) exit;

/**
 * IpCmsException class
 */
class Exception extends \Exception
{
    //error codes
    const DB = 0;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    

}