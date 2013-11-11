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
    protected static $request = null;
    protected static $config = null;


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
        /**
         * @var $log \Ip\Module\Log\Module
         */
        global $log;
        return $log;
    }

    /**
     * @return Dispatcher
     */
    public static function getDispatcher()
    {
        /**
         * @var $dispatcher \Ip\Dispatcher
         */
        global $dispatcher;
        return $dispatcher;
    }

    /**
     * @return \Site
     */
    public static function getSite()
    {
        /**
         * @var $site \Site
         */
        global $site;
        return $site;
    }


    /**
     * @return \Ip\Frontend\Session
     */
    public static function getSession()
    {
        /**
         * @var $session \Ip\Frontend\Session
         */
        global $session;
        return $session;
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
     * @return \Ip\Internal\Request
     */
    public static function getRequest()
    {
        if (self::$request == null) {
            self::$request = new \Ip\Internal\Request();
        }

        return self::$request;
    }

    public static function replaceRequestService($request)
    {
        self::$request = $request;
    }
}