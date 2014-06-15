<?php


namespace Ip\Exception;


/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
class NotImplemented extends \Ip\Exception
{

    public function __construct($message = null, $data = null, $code = 0, \Exception $previous = null)
    {
        if ($message === null) {
            $message = 'Not implemented.';
        }
        parent::__construct($message, $code, $previous);
    }


}
