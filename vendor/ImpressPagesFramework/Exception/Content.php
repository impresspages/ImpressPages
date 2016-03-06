<?php

namespace Ip\Exception;

class Content extends \Ip\Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, null,  $code, $previous);
    }


}
