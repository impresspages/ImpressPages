<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

if (!defined('CMS')) exit;
/**
 * Autoloader class
 */


function __impressPagesAutoloader($name) {

    $fileName = str_replace('\\', '/', $name) . '.php';

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
    }

    return false;
}


function __impressPagesAutoloaderTry($fileName) {
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