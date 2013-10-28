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

    public static function libraryUrl($path)
    {
        return static::$rawConfig['BASE_URL'] . static::$rawConfig['LIBRARY_DIR'] . $path;
    }

    public static function libraryFile($path)
    {
        return static::$rawConfig['BASE_DIR'] . static::$rawConfig['LIBRARY_DIR'] . $path;
    }

    public static function themeUrl($path, $vars = null)
    {
        $url = static::$rawConfig['BASE_URL'] . static::$rawConfig['THEME_DIR'] . static::$rawConfig['THEME'] . '/' . $path;
        if ($vars) {
            $url .= '?' . http_build_query($vars);
        }

        return $url;
    }

    public static function coreUrl($path)
    {
        return static::$rawConfig['BASE_URL'] . static::$rawConfig['CORE_DIR'] . $path;
    }

    public static function oldModuleUrl($path)
    {
        return static::$rawConfig['BASE_URL'] . static::$rawConfig['MODULE_DIR'] . $path;
    }

    public static function oldModuleFile($path)
    {
        return static::$rawConfig['BASE_DIR'] . static::$rawConfig['MODULE_DIR'] . $path;
    }

//    public static function pluginDir()
//    {
//        return static::$config['pluginDir'];
//    }
//
//    public static function fileDir()
//    {
//        return static::$config['fileDir'];
//    }
//
//    public static function homeUrl()
//    {
//        return static::$config['homeUrl'];
//    }
//
//    /**
//     * @environment
//     */
//    public static function isFrontend()
//    {
//
//    }
//
//    /**
//     * @environment
//     */
//    public static function isBackend()
//    {
//
//    }
//
//    /**
//     * @environment
//     */
//    public static function isDevelopmentEnvironment()
//    {
//
//    }
//
//    public static function shouldShowErrors()
//    {
//
//    }
//
//    public static function errorReportingEmail()
//    {
//
//    }
//
//    public static function includeDir()
//    {
//
//    }
//
//    public static function libraryDir()
//    {
//
//    }
//
//    public static function moduleDir()
//    {
//
//    }

    public static function init($config)
    {
        self::$rawConfig = $config;

        foreach ($config as $key => $value) {
            if (preg_match('%^[_A-Z]+$%', $key) && !defined($key)) {
                define($key, $value);
            }
        }

        // TODOX check
        static::$config['LIBRARY_URL'] = static::$rawConfig['BASE_URL'] . static::$rawConfig['LIBRARY_URL'];

//        $relativeDirs = array(
//            'fileDir',
//            'pluginDir',
//        );
//
//        foreach ($relativeDirs as $relativeDir) {
//            if (self::$rawConfig[$relativeDir][0] == '.') {
//                self::$config[$relativeDir] = self::$rawConfig['baseDir'] . substr(self::$rawConfig[$relativeDir], 1);
//            } else {
//                self::$config[$relativeDir] = self::$rawConfig[$relativeDir];
//            }
//        }
//
//        self::$config['homeUrl'] = self::$rawConfig['protocol'] . '://' . static::$rawConfig['host'] . static::$rawConfig['siteUrlPath'];
    }
}