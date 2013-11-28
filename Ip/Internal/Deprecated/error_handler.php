<?php

/**
 * @package		Library
 *
 *
 */

//TODOX refactor
/**
 * Error handler for all errors and warnings. Depending on configuration he
 * prints error to output, sends by the email and logs to database.
 * Maximum emails count is 10 per hour.
 */
function myErrorHandler ($errno, $errstr, $errfile, $errline) {
    $originalIpErrorHandler = set_error_handler("ipSilentErrorHandler");

    $type = 'php.';
    switch ($errno) {
        case E_USER_WARNING:
            $type .= 'WARNING';
            break;
        case E_USER_NOTICE:
            $type .= 'NOTICE';
            break;
        case E_WARNING:
            $type .= 'WARNING';
            break;
        case E_NOTICE:
            $type .= 'NOTICE';
            break;
        case E_CORE_WARNING:
            $type .= 'WARNING';
            break;
        case E_COMPILE_WARNING:
            $type .= 'WARNING';
            break;
        case E_USER_ERROR:
            $type .= 'ERROR';
            break;
        case E_ERROR:
            $type .= 'ERROR';
            break;
        case E_PARSE:
            $type .= 'PARSE';
            break;
        case E_CORE_ERROR:
            $type .= 'ERROR';
            break;
        case E_COMPILE_ERROR:
            $type .= 'ERROR';
            break;
        default:
            $type .= 'UNKNOWN_EXCEPTION';
            break;
    }

    if (class_exists('Ip\Module\Log\Logger')) {
        ipLog()->error($type . ': ' . $errstr . ' in {file}:{line}', array('file' => $errfile, 'line' => $errline));
    }

    if(ipConfig()->getRaw('ERRORS_SHOW')){
        restore_error_handler();
        throw new \Exception("{$errstr} in {$errfile}:{$errline}", $errno);
    }


    restore_error_handler();
}
$old_error_handler = set_error_handler("myErrorHandler");


function ipSilentErrorHandler($errno, $errstr, $errfile, $errline) {
    //do nothing
}