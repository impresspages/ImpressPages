<?php 
/**
 * @package ImpressPages
 *
 *
 */

namespace IpUpdate\Gui;

class AjaxErrorHandler
{

    public static function init()
    {
        set_error_handler(array(__CLASS__, 'captureError'));
        set_exception_handler(array( __CLASS__, 'captureException'));
        register_shutdown_function(array(__CLASS__, 'captureShutdown'));
    }

    public static function captureError($errno, $errstr, $errfile, $errline)
    {
        if (ini_get('display_errors')) {
            $message = self::getErrorMessage($errno, $errstr, $errfile, $errline);
            self::reportError($message);
            exit;
        }
    }
    
    public static function captureException(\Exception $e)
    {
        if (ini_get('display_errors')) {
            $message = self::getErrorMessage(1, $e->getMessage(), $e->getFile(), $e->getLine());
            self::reportError($message);
            exit;
        }
    }    
    
    public static function captureShutdown()
    {
        $error = error_get_last();
        if (!$error) {
            return;
        }
        
        if (ini_get('display_errors')) {
            if (!headers_sent()) {
                ob_end_clean( );
            }
            $message = self::getErrorMessage($error['type'], $error['message'], $error['file'], $error['line']);
            self::reportError($message);
        }
        return false;
    }
    

    public static function reportError($errorMessage)
    {
        $view = \IpUpdate\Gui\View::create('Update/error_unknown.php', array('errorMessage' => $errorMessage));
        $html = $view->render();
        if (headers_sent()) {
            echo $html;
        } else {
            header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
            $data = array(
                'html' => $html
            );
            $output = json_encode(self::utf8Encode($data));
            echo $output;
        }
    }

    /**
     *
     *  Returns $dat encoded to UTF8
     * @param mixed $dat array or string
     */
    private static function utf8Encode($dat)
    {
        if (is_string($dat)) {
            if (mb_check_encoding($dat, 'UTF-8')) {
                return $dat;
            } else {
                return utf8_encode($dat);
            }
        }
        if (is_array($dat)) {
            $answer = array();
            foreach($dat as $i=>$d) {
                $answer[$i] = self::utf8Encode($d);
            }
            return $answer;
        }
        return $dat;
    }
    
    private static function getErrorMessage($errno, $errstr, $errfile, $errline)
    {
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
        }

        $message = '<b>'.$message.'</b> '.htmlspecialchars($errstr).'<br /> in '.$errfile.' on line '.$errline.'';
        return $message;
    }
}