<?php
/**
 * @package ImpressPages

 *
 */

namespace Modules\standard\content_management;


if (!defined('CMS')) exit;

/**
 * IpCmsException class
 */
class Exception extends \Exception
{
    //error codes
    const UNKNOWN = 0;
    const DB = 1;
    const UNKNOWN_WIDGET = 2;
    const UNKNOWN_INSTANCE = 3;
    const UNKNOWN_REVISION = 4;
    const NO_LAYOUTS = 5;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
    

}