<?php

namespace Ip\Exception;

/**
 * This exception does not extends Ip\Exception!
 *
 * Purpose of this exception is to show error on the line database method was called.
 *
 * @package Ip\Exception
 */
class Db extends \PDOException
{
    public function __construct($message = "", $code = 0, \PDOException $previous = null)
    {
        $this->message = $message;
        if ($previous) {
            $this->message = $previous->message;
            $this->code = $previous->code;
            $this->file = $previous->file;
            $this->line = $previous->line;
            $this->trace = $previous->getTrace();
            $this->previous = $previous;
        }

        $backtrace = debug_backtrace();

        // We need directory separator for Windows
        $ipDbPath = 'Ip' . DIRECTORY_SEPARATOR . 'Db.php';
        $pathLength = strlen($ipDbPath);

        // We usually want exception to show error in the code that uses Db class
        foreach ($backtrace as $info) {
            if (substr($info['file'], -$pathLength) != $ipDbPath) {
                $this->file = $info['file'];
                $this->line = $info['line'];
                break;
            }
        }
    }
}
