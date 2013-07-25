<?php
/**
 * @package ImpressPages

 *
 */


namespace IpUpdate\Library;

/**
 * Update process error
 */
class UpdateException extends \Exception
{
    //error codes
    const UNKNOWN = 0;
    const WRITE_PERMISSION = 1;
    const IN_PROGRESS = 2;
    const CURL_REQUIRED = 3;
    const WRONG_CHECKSUM = 4;
    const SQL = 5;
    const EXTRACT_FAILURE = 6;
    const NO_UPDATE = 7;

    private $data;
    
    // Redefine the exception so message isn't optional
    public function __construct($message, $code, $data = array(), \Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
        
        $this->data = $data;
    }
    
    public function getValue($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }
}