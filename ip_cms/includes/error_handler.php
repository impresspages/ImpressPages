<?php

/**
 * @package		Library
 *
 *
 */


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

    if(ERRORS_SHOW){
        restore_error_handler();
        throw new \Ip\PhpException($message, $errno);
    }
    if($log && defined('ERRORS_SEND') && ERRORS_SEND != ''){
        require_once(BASE_DIR.MODULE_DIR.'administrator/email_queue/module.php');
        $logsCount = $log->lastLogsCount(60, 'system/error');
        if($logsCount <= 9){
            if($logsCount == 9)
            $message .= '

Error emails count has reached the limit. See logs for more errors.';

            $queue = new \Modules\administrator\email_queue\Module();
            if($parametersMod) //if parameters module not initialized yet, it will only throw new one error. So, use it only if it is initialized
            $queue->addEmail($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'), ERRORS_SEND, '', BASE_URL." ERROR", $message, false, true);
            else
            $queue->addEmail(ERRORS_SEND, '', ERRORS_SEND, '', BASE_URL." ERROR", $message, false, true);
            $queue->send();


            $log->log('system/error', 'Sent e-mail to '.ERRORS_SEND, $message);
        }
    }


    restore_error_handler();
}
$old_error_handler = set_error_handler("myErrorHandler");


function ipSilentErrorHandler($errno, $errstr, $errfile, $errline) {
    //do nothing
}