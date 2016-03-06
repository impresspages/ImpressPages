<?php
/**
 * @package ImpressPages
 */

namespace Ip;


/**
 * Exception class used to throw exceptions instead of default PHP warnings and notices
 */
class PhpException extends \Exception
{

    /**
     * Redefine the exception so message isn't optional     *
     * @param string $message
     * @param int $code PHP err code E_USER_WARNING, E_USER_NOTICE, E_WARNING, etc.
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}
