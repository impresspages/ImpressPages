<?php

namespace Ip;

/**
 * Purpose of this exception is to show error on the line db method was called.
 * @package Ip
 */
class DbException extends \PDOException
{
    public function __construct($message = "", $code = 0, \PDOException $previous = null)
    {
        $this->message = $previous->message;
        $this->code = $previous->code;
        $this->file = $previous->file;
        $this->line = $previous->line;
        $this->trace = $previous->getTrace();
        $this->previous = $previous;

        $backtrace = debug_backtrace();

        // We need directory separator for Windows
        $dbFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Db.php';

        foreach ($backtrace as $info) {
            if ($info['file'] != $dbFile) {
                $this->file = $info['file'];
                $this->line = $info['line'];
                break;
            }
        }
    }
}
