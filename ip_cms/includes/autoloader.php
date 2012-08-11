<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

if (!defined('CMS')) exit;
/**
 * Autoloader class
 */


function __impressPagesAutoloader($name) {

    $fileName = str_replace('\\', '/', $name) . '.php';
    if($fileName[0] == '/') { //in some environments required class starts with slash. In that case remove the slash.
        $fileName = substr($fileName, 1);
    }

    if (file_exists(BASE_DIR.INCLUDE_DIR.$fileName)) {
        require_once(BASE_DIR.INCLUDE_DIR.$fileName);
        return true;
    }

    if (substr($fileName, 0, 8) == 'Modules/') {
        $fileName = substr($fileName, 8);
        $success = __impressPagesAutoloaderTry($fileName);
        if ($success) {
            return true;
        }
        $success = __impressPagesAutoloaderTry(strtolower($fileName));
        if ($success) {
            return true;
        }
    }
    
    if (substr($fileName, 0, 8) == 'Library/') {
        $fileName = substr($fileName, 8);
        if (file_exists(BASE_DIR.LIBRARY_DIR.$fileName)) {
            require_once(BASE_DIR.LIBRARY_DIR.$fileName);
            return true;
        }

        if (substr($fileName, 0, 9) == 'Php/Text/') {
            $fileName = 'php/text/' . substr($fileName, 9);
        }

        if (substr($fileName, 0, 19) == 'Php/Image/Functions') {
            $fileName = 'php/image/functions' . substr($fileName, 19);
        }

        if (substr($fileName, 0, 18) == 'Php/File/Functions') {
            $fileName = 'php/file/functions' . substr($fileName, 18);
        }

        if (substr($fileName, 0, 4) == 'Php/') {
            $fileName = 'php/' . substr($fileName, 4);
        }
        //second try
        if (file_exists(BASE_DIR.LIBRARY_DIR.$fileName)) {
            require_once(BASE_DIR.LIBRARY_DIR.$fileName);
            return true;
        }
    }

    return false;
}


function __impressPagesAutoloaderTry($fileName) {
    $baseName = basename($fileName);
    if ($baseName == 'config.php' || $baseName == 'Config.php') {
        if (file_exists(BASE_DIR.CONFIG_DIR.$fileName)) {
            require_once(BASE_DIR.CONFIG_DIR.$fileName);
            return true;
        }
    }
    if (file_exists(BASE_DIR.MODULE_DIR.$fileName)) {
        require_once(BASE_DIR.MODULE_DIR.$fileName);
        return true;
    }
    if (file_exists(BASE_DIR.PLUGIN_DIR.$fileName)) {
        require_once(BASE_DIR.PLUGIN_DIR.$fileName);
        return true;
    }
}

spl_autoload_register('__impressPagesAutoloader');