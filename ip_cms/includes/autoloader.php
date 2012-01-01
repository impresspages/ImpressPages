<?php

/**
 * @package		Library
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

    if (file_exists(BASE_DIR.MODULE_DIR.$fileName)) {
        require_once(BASE_DIR.MODULE_DIR.$fileName);
        return true;
    }

    $parts = explode('\\', $name);
    if (count($parts) >= 4 && $parts[0] == 'Modules') {
        $fileName = $parts[1].'/'.$parts[2].'/'.strtolower($parts[3]);
        for ($depth = 4; $depth < count($parts) - 1; $depth++) {
            $fileName .= '/'.strtolower($parts[$depth]); 
        }
        $fileName .= '.php';
        if (file_exists(BASE_DIR.INCLUDE_DIR.$fileName)) {
            require_once(BASE_DIR.INCLUDE_DIR.$fileName);
            return true;
        }
        if (file_exists(BASE_DIR.MODULE_DIR.$fileName)) {
            require_once(BASE_DIR.MODULE_DIR.$fileName);
            return true;
        }        
    }
    
    return false;
}

spl_autoload_register('__impressPagesAutoloader');