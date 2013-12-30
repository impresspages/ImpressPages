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
    protected static $permissions;

    protected static $serviceClasses = array(
        'db' => '\Ip\Db',
        'options' => '\Ip\Options',
        'storage' => '\Ip\Storage',
        'log' => '\Ip\Internal\Log\Logger',
        'translator' => '\Ip\Translator\Translator',
        'dispatcher' => '\Ip\Dispatcher',
        'response' => '\Ip\Response\Layout',
        'content' => '\Ip\Content',
        'permissions' => '\Ip\Internal\Permissions\UserPermissions',
    );

    /**
     * @return \Ip\Options
     */
    public static function options()
    {
        if (self::$options == null) {
            self::$options = static::loadService('options');
        }
        return self::$options;
    }


    /**
     * @return \Ip\Storage
     */
    public static function storage()
    {
        if (self::$storage == null) {
            self::$storage = static::loadService('storage');
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

        $serviceClasses = $config->getRaw('SERVICES');
        if ($serviceClasses) {
            static::$serviceClasses = array_merge(static::$serviceClasses, $serviceClasses);
        }
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public static function log()
    {
        if (self::$log == null) {
            self::$log = static::loadService('log');
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
     * Add new request to HMVC queue
     * Used by Application. Never add requests manually.
     * @param $request
     */
    public static function addRequest($request)
    {
        self::$requests[] = $request;
        self::$dispatchers[] = static::loadService('dispatcher');
        self::$contents[] = static::loadService('content');
        self::$responses[] = static::loadService('response');
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
        if (static::$db === null) {
            static::$db = static::loadService('db');
        }

        return static::$db;
    }

    /**
     * @return \Ip\Translator\Translator
     */
    public static function translator()
    {
        if (static::$translator === null) {
            static::$translator = static::loadService('translator');
        }

        return static::$translator;
    }

    protected static function loadService($name)
    {
        return new static::$serviceClasses[$name]();
    }

    /**
     * @return \Ip\Internal\Permissions\UserPermissions
     */
    public static function permissions()
    {
        if (static::$permissions === null) {
            static::$permissions = static::loadService('permissions');
        }

        return static::$permissions;
    }

}