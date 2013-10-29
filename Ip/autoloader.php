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

    if (file_exists(BASE_DIR.$fileName)) {
        require_once(BASE_DIR.$fileName);
        return true;
    }

    if (file_exists(\Ip\Config::includePath($fileName))) {
        require_once \Ip\Config::includePath($fileName);
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
        if (file_exists(\Ip\Config::libraryFile($relativeFileName))) {
            require_once \Ip\Config::libraryFile($relativeFileName);
            return true;
        }

        if (!empty($path[1]) && $path[1] == 'Php') { // Library\Php

            if ($path[2] == 'Text') { // Library\Php\Text
                $relativeFileName = 'php/text/' . substr($fileName, 17);
            } elseif ($path[2] == 'Image' && $path[3] == 'Functions.php') {
                $relativeFileName = 'php/image/functions.php';
            } elseif ($path[2] == 'File' && $path[3] == 'Functions.php') {
                $relativeFileName = 'php/file/functions.php';
            } else {
                $relativeFileName = 'php/' . substr($fileName, 12);
            }

            //second try
            if (file_exists(\Ip\Config::libraryFile($relativeFileName))) {
                require_once \Ip\Config::libraryFile($relativeFileName);
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
        if (file_exists(\Ip\Config::libraryFile('php/pclzip/PclZip.php'))) {
            require_once \Ip\Config::libraryFile('php/pclzip/PclZip.php');
            return true;
        }
    }

    return false;
}


function __impressPagesAutoloaderTry($fileName) {
    if (file_exists(\Ip\Config::oldModuleFile($fileName))) {
        require_once \Ip\Config::oldModuleFile($fileName);
        return true;
    }
}

spl_autoload_register('__impressPagesAutoloader');