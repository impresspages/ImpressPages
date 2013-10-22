<?php
/**
 * @package ImpressPages
 */

namespace Ip;


class Config
{
    protected static $rawConfig = array();
    protected static $config = array();

    public function getCoreModuleUrl()
    {
        return BASE_URL.'Ip/Module/';
    }

    public static function getRaw($name)
    {
        return array_key_exists($name, static::$rawConfig) ? static::$rawConfig[$name] : null;
    }

    public static function pluginDir()
    {
        return static::$config['pluginDir'];
    }

    public static function fileDir()
    {
        return static::$config['fileDir'];
    }

    public static function homeUrl()
    {
        return static::$config['homeUrl'];
    }

    /**
     * @environment
     */
    public static function isFrontend()
    {

    }

    /**
     * @environment
     */
    public static function isBackend()
    {

    }

    /**
     * @environment
     */
    public static function isDevelopmentEnvironment()
    {

    }

    public static function shouldShowErrors()
    {

    }

    public static function errorReportingEmail()
    {

    }

    public static function includeDir()
    {

    }

    public static function libraryDir()
    {

    }

    public static function moduleDir()
    {

    }

    public static function init($config)
    {
        self::$rawConfig = $config;

        $relativeDirs = array(
            'fileDir',
            'pluginDir',
        );

        foreach ($relativeDirs as $relativeDir) {
            if (self::$rawConfig[$relativeDir][0] == '.') {
                self::$config[$relativeDir] = self::$rawConfig['baseDir'] . substr(self::$rawConfig[$relativeDir], 1);
            } else {
                self::$config[$relativeDir] = self::$rawConfig[$relativeDir];
            }
        }

        self::$config['homeUrl'] = self::$rawConfig['protocol'] . '://' . static::$rawConfig['host'] . static::$rawConfig['siteUrlPath'];
    }
}