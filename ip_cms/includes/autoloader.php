<?php

/**
 * @package ImpressPages
 *
 *
 */


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

    $path = explode('/', $fileName);

    if ($path[0] == 'Modules') {
        $relativeFileName = substr($fileName, 8);
        $success = __impressPagesAutoloaderTry($relativeFileName);
        if ($success) {
            return true;
        }
        $success = __impressPagesAutoloaderTry(strtolower($relativeFileName));
        if ($success) {
            return true;
        }
    } elseif ($path[0] == 'Library') {
        $relativeFileName = substr($fileName, 8);
        if (file_exists(BASE_DIR.LIBRARY_DIR.$relativeFileName)) {
            require_once(BASE_DIR.LIBRARY_DIR.$relativeFileName);
            return true;
        }

        if (!empty($path[1]) && $path[1] == 'Php') { // Library\Php
            if ($path[2] == 'Text') { // Library\Php\Text
                $relativeFileName = 'php/text/' . substr($fileName, 17);
            } elseif ($path[2] == 'Image' && $path[3] == 'Functions') {
                $relativeFileName = 'php/image/functions' . substr($fileName, 19);
            } elseif ($path[2] == 'File' && $path[3] == 'Functions') {
                $relativeFileName = 'php/file/functions' . substr($fileName, 18);
            } else {
                $relativeFileName = 'php/' . substr($fileName, 4);
            }

            //second try
            if (file_exists(BASE_DIR.LIBRARY_DIR.$relativeFileName)) {
                require_once(BASE_DIR.LIBRARY_DIR.$relativeFileName);
                return true;
            }
        }

    } elseif ($path[0] == 'Plugin') {

        if (file_exists(BASE_DIR . $fileName)) {

            require_once(BASE_DIR . $fileName);
            return true;
        }
    }

    if ($fileName == 'PclZip.php') {
        if (file_exists(BASE_DIR.LIBRARY_DIR.'php/pclzip/PclZip.php')) {
            require_once(BASE_DIR.LIBRARY_DIR.'php/pclzip/PclZip.php');
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