<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Library\Helper;

/**
 * Update process error
 */
class FileSystemException extends \IpUpdate\Library\UpdateException
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code, \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
        
    }
    
}