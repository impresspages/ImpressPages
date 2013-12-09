<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip;




/**
 *
 * Locate system services
 *
 */
class ServiceLocator
{
    protected static $requests = array();
    protected static $dispatchers = array();
    protected static $contents = array();
    protected static $responses = array();
    protected static $config = null;
    protected static $log = null;
    protected static $options = null;
    protected static $storage = null;
    protected static $db;
    protected static $translator;

    public static function options()
    {
        if (self::$options == null) {
            self::$options = new \Ip\Options();
        }
        return self::$options;
    }


    public static function storage()
    {
        if (self::$storage == null) {
            self::$storage = new \Ip\Storage();
        }
        return self::$storage;
    }

    /**
     * @return \Ip\Config
     */
    public static function config()
    {
        return self::$config;
    }

    public static function setConfig($config)
    {
        self::$config = $config;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public static function log()
    {
        if (self::$log == null) {
            self::$log= new \Ip\Module\Log\Logger();
        }
        return self::$log;
    }

    /**
     * @return Dispatcher
     */
    public static function dispatcher()
    {
        return end(self::$dispatchers);
    }



    /**
     * @return \Ip\Application
     */
    public static function application()
    {
        global $application;
        return $application;
    }



    /**
     * @return \ParametersMod
     */
    public static function getParametersMod()
    {
        /**
         * @var $session \ParametersMod
         */
        global $parametersMod;
        return $parametersMod;
    }

    /**
     * Add new request to HMVC queue
     * Used by Application. Never add requests manually.
     * @param $request
     */
    public static function addRequest($request)
    {
        self::$requests[] = $request;
        self::$dispatchers[] = new \Ip\Dispatcher();
        self::$contents[] = new \Ip\Content();
        self::$responses[] = new \Ip\Response\Layout();
    }

    /**
     * Remove request from HMVC. Last request should always stay intact and can't be removed as it is needed for application close action
     */
    public static function removeRequest()
    {
        if (count(self::$requests) >1 ) {
            array_pop(self::$dispatchers);
            array_pop(self::$requests);
            array_pop(self::$contents);
            array_pop(self::$responses);
        }
    }

    /**
     * @return \Ip\Request
     */
    public static function request()
    {
        return end(self::$requests);
    }

    /**
     * @return \Ip\Content
     */
    public static function content()
    {
        return end(self::$contents);
    }


    /**
     * @return \Ip\Response
     */
    public static function response()
    {
        return end(self::$responses);
    }

    /**
     * @param Response $response
     */
    public static function setResponse(\Ip\Response $response)
    {
        end(self::$responses);
        self::$responses[] = $response;
    }

    /**
     * @return \Ip\Db
     */
    public static function db()
    {
        if (self::$db === null) {
            self::$db = new \Ip\Db();
        }

        return self::$db;
    }

    /**
     * @return \Ip\Translator\Translator
     */
    public static function translator()
    {
        if (static::$translator === null) {
            static::$translator = new \Ip\Translator\Translator();
        }

        return static::$translator;
    }
}