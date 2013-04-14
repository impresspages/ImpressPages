<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\administrator\repository;



/**
 * IpCmsException class
 */
class UploadException extends \Exception
{
    //error codes
    const NO_PERMISSION = 1;
    const FORBIDDEN_FILE_EXTENSION = 2;
    const INPUT_STREAM_ERROR = 3;
    const OUTPUT_STREAM_ERROR = 4;
    const FAILED_TO_MOVE_UPLOADED_FILE = 5;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }


}