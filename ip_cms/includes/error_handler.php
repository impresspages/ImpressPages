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

    global $log;
    global $parametersMod;
    $message = '';
    switch ($errno) {
        case E_USER_WARNING:
            $message .= 'WARNING ';
            break;
        case E_USER_NOTICE:
            $message .= 'NOTICE ';
            break;
        case E_WARNING:
            $message .= 'WARNING ';
            break;
        case E_NOTICE:
            $message .= 'NOTICE ';
            break;
        case E_CORE_WARNING:
            $message .= 'WARNING ';
            break;
        case E_COMPILE_WARNING:
            $message .= 'WARNING ';
            break;
        case E_USER_ERROR:
            $message .= 'ERROR ';
            break;
        case E_ERROR:
            $message .= 'ERROR ';
            break;
        case E_PARSE:
            $message .= 'PARSE ';
            break;
        case E_CORE_ERROR:
            $message .= 'ERROR ';
            break;
        case E_COMPILE_ERROR:
            $message .= 'ERROR ';
            break;
        default:
            $message .= 'UNKNOWN EXCEPTION ';
            break;
    }


    $message = $message.' '.$errstr.' in '.$errfile.' on line '.$errline.'';
    if ($log){ //if log module not initialized yet, it will only throw new error. So, use it only if it is initialized
        $log->log('system', 'error', $message);
    }

    if(\Ip\Config::getRaw('ERRORS_SHOW')){
        restore_error_handler();
        throw new \Ip\PhpException($message, $errno);
    }
    if($log && \Ip\Config::getRaw('ERRORS_SEND')){
        $logsCount = $log->lastLogsCount(60, 'system/error');
        if($logsCount <= 9){
            if($logsCount == 9)
            $message .= '

Error emails count has reached the limit. See logs for more errors.';

            $queue = new \Ip\Module\Email\Module();
            if($parametersMod) //if parameters module not initialized yet, it will only throw new one error. So, use it only if it is initialized
            $queue->addEmail(ipGetOption('Config.websiteEmail'), ipGetOption('Config.websiteEmail'), ERRORS_SEND, '', \Ip\Config::baseUrl('')." ERROR", $message, false, true);
            else
            $queue->addEmail(\Ip\Config::getRaw('ERRORS_SEND'), '', \Ip\Config::getRaw('ERRORS_SEND'), '', \Ip\Config::baseUrl('')." ERROR", $message, false, true);
            $queue->send();


            $log->log('system/error', 'Sent e-mail to '.\Ip\Config::getRaw('ERRORS_SEND'), $message);
        }
    }


    restore_error_handler();
}
$old_error_handler = set_error_handler("myErrorHandler");


function ipSilentErrorHandler($errno, $errstr, $errfile, $errline) {
    //do nothing
}