<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management;


if (!defined('CMS')) exit;

/**
 * IpCmsException class
 */
class Exception extends \Exception
{
    //error codes
    const DB = 0;
    const UNKNOWN_WIDGET = 1;
    const UNKNOWN_INSTANCE = 2;
    const UNKNOWN_REVISION = 3;
    const NO_LAYOUTS = 4;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}