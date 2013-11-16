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


    public static function getOptions()
    {
        return new \Ip\Options();
    }


    public static function getStorage()
    {
        return new \Ip\Storage();
    }

    public static function getConfig()
    {
        if (self::$config == null) {
            self::$config = new \Ip\Config();
        }
        return self::$config;
    }

    /**
     * @return \Ip\Module\Log\Module
     */
    public static function getLog()
    {
        if (self::$log== null) {
            self::$log= new \Ip\Module\Log\Module();
        }
        return self::$log;
    }

    /**
     * @return Dispatcher
     */
    public static function getDispatcher()
    {
        return end(self::$dispatchers);
    }



    /**
     * @return \Ip\Core\Application
     */
    public static function getApplication()
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
     * @return \Ip\Internal\Request
     */
    public static function getRequest()
    {
        return end(self::$requests);
    }

    public static function replaceRequestService($request)
    {
        array_pop(self::$requests);
        self::$requests[] = $request;
    }

    /**
     * @return \Ip\Content
     */
    public static function getContent()
    {
        return end(self::$contents);
    }


    /**
     * @return \Ip\Content
     */
    public static function getResponse()
    {
        return end(self::$responses);
    }
}