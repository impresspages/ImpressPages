<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Update\Helper;

/**
 * Update process error
 */
class FileSystemException extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code, \Exception $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);

    }

}
