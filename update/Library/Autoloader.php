<?php

namespace IpUpdate\Library;

/**
 * @package ImpressPages
 *
 *
 */


class Autoloader
{
    private static $dirs;
    
    public function register($rootDir)
    {
        if (empty($dirs)) {
            spl_autoload_register('IpUpdate\Library\Autoloader::load');
        }
        static::$dirs[] = $rootDir;
    }

    public static function load($name)
    {
        if (strpos($name, 'IpUpdate\\Library\\') !== 0) {
            return false;
        }
        
        $name = substr($name, 17);
        
        
        $fileName = str_replace('\\', '/', $name) . '.php';
        if($fileName[0] == '/') { //in some environments required class starts with slash. In that case remove the slash.
            $fileName = substr($fileName, 1);
        }

        return self::tryFile($fileName);
    }

    private static function tryFile($fileName)
    {
        if (empty(static::$dirs)) {
            return false;
        }
        foreach(static::$dirs as $dir) {
            if (file_exists($dir.$fileName)) {
                require_once($dir.$fileName);
                return true;
            }
        }
    }
}




