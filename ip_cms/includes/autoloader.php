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
    
    $fileName = BASE_DIR.INCLUDE_DIR.str_replace('\\', '/', $name) . '.php';
    
    if (file_exists($fileName)) {
        require_once($fileName);
        return true;   
    }

    $fileName = BASE_DIR.MODULE_DIR.str_replace('\\', '/', $name) . '.php';
    
    if (file_exists($fileName)) {
        require_once($fileName);
        return true;   
    }
    
    return false;
}

spl_autoload_register('__impressPagesAutoloader');