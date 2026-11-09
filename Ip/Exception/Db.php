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
    public function __construct($message = "", $code = 0, ?\PDOException $previous = null)
    {
        // PDO error codes are SQLSTATE strings, so the parent gets 0 and the real code is copied below.
        parent::__construct($previous ? $previous->getMessage() : $message, 0, $previous);
        if ($previous) {
            $this->code = $previous->getCode();
            $this->file = $previous->getFile();
            $this->line = $previous->getLine();
        }

        $backtrace = debug_backtrace();

        // We need directory separator for Windows
        $ipDbPath = 'Ip' . DIRECTORY_SEPARATOR . 'Db.php';
        $pathLength = strlen($ipDbPath);

        // We usually want exception to show error in the code that uses Db class
        foreach ($backtrace as $info) {
            if (isset($info['file']) && substr($info['file'], -$pathLength) != $ipDbPath) {
                $this->file = $info['file'];
                $this->line = $info['line'];
                break;
            }
        }
    }
}
