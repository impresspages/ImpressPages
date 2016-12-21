<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal;


class ErrorHandler
{


    public static function ipErrorHandler($errno, $errstr, $errfile, $errline)
    {
        set_error_handler(__CLASS__ . '::ipSilentErrorHandler');

        $type = '';
        switch ($errno) {
            case E_USER_WARNING:
                $type .= 'Warning';
                break;
            case E_USER_NOTICE:
                $type .= 'Notice';
                break;
            case E_WARNING:
                $type .= 'Warning';
                break;
            case E_NOTICE:
                $type .= 'Notice';
                break;
            case E_CORE_WARNING:
                $type .= 'Warning';
                break;
            case E_COMPILE_WARNING:
                $type .= 'Warning';
                break;
            case E_USER_ERROR:
                $type .= 'Error';
                break;
            case E_ERROR:
                $type .= 'Error';
                break;
            case E_PARSE:
                $type .= 'Parse';
                break;
            case E_CORE_ERROR:
                $type .= 'Error';
                break;
            case E_COMPILE_ERROR:
                $type .= 'Error';
                break;
            default:
                $type .= 'Unknown exception';
                break;
        }

        if (class_exists('Ip\Internal\Log\Logger') && ipConfig()->database()) {
            ipLog()->error($type . ': ' . $errstr . ' in {file}:{line}', array('file' => $errfile, 'line' => $errline));
        }

        if (ipConfig()->showErrors()) {
            echo "{$errstr} in {$errfile}:{$errline}";
        }


        restore_error_handler();
    }


    public static function ipSilentErrorHandler($errno, $errstr, $errfile, $errline)
    {
        //do nothing
    }
}
