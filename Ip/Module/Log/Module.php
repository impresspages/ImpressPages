<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Log;



/**
 * Logging class. Normaly all logs should be done trouht this class
 * @package ImpressPages
 */
class Module{
    /** @access private*/

    /**
     * @access private
     */
    function __construct(){
    }

    /**
     * Add new log to db
     * @param string $module 'modulegroup/modulename'
     * @param string $name Meaning of log: error, status, action and so on.
     * @param string $valueStr
     * @param int $valueInt
     * @param float $valueFloat
     * @return null
     */
    public static function log($module, $name, $valueStr = null , $valueInt = null, $valueFloat = null){
        Db::log($module, $name, $valueStr, $valueInt, $valueFloat);
    }

    /**
     * The same as log, but sends an email.
     * @param string $module 'modulegroup/modulename'
     * @param string $name Meaning of log: error, status, action and so on.
     * @param string $valueStr
     * @param int $valueInt
     * @param float $valueFloat
     * @return null
     */
    public static function logAndReport($module, $name, $valueStr = null , $valueInt = null, $valueFloat = null)
    {
        self::log($module, $name, $valueStr, $valueInt, $valueFloat);
        if (\Ip\Config::getRaw('ERRORS_SEND')) {
            $queue = new \Ip\Module\Email\Module();
            $message = 'SERVER ERROR<br/><br/>';
            $message .= $module.'<br/>';
            $message .= $name.'<br/>';
            $message .= $valueStr.'<br/>';
            $message .= $valueInt.'<br/>';
            $message .= $valueFloat.'<br/>';
            $queue->addEmail(\Ip\Config::getRaw('ERRORS_SEND'), '', \Ip\Config::getRaw('ERRORS_SEND'), '', \Ip\Config::baseUrl('')." CRITICAL ERROR", $message, true, true);
            $queue->send();
        }

    }

    /**
     * @param int $minutes
     * @param string $module
     * @return int count of logs, made by specified module in last $minutes
     */
    public static function lastLogsCount($minutes, $module = null, $name = null){
        return Db::lastLogsCount($minutes, $module, $name);
    }

    /**
     * @param int $minutes
     * @param string $module
     * @return int count of logs, made by specified module in last $minutes
     */
    public static function lastLogs($minutes, $module = null, $name = null){
        return Db::lastLogs($minutes, $module, $name);
    }

}



