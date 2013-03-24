<?php
/**
 *
 * ImpressPages CMS main frontend file
 *
 * This file iniciates required variables and outputs the content.
 *
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license see ip_license.html
 */

/** Make sure files are accessed through index. */

if (!defined('CMS')) {
    define('CMS', true); // make sure other files are accessed through this file.
}
if (!defined('FRONTEND')) {
    define('FRONTEND', true); // make sure other files are accessed through this file.
}


if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages CMS you need PHP 5.3.*';
    exit;
}


if(is_file(__DIR__.'/ip_config.php')) {
    require (__DIR__.'/ip_config.php');
} else {
    require (__DIR__.'/../ip_config.php');
}


error_reporting(E_ALL|E_STRICT);
if (DEVELOPMENT_ENVIRONMENT){ 
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}



require (BASE_DIR.INCLUDE_DIR.'parameters.php');
require (BASE_DIR.INCLUDE_DIR.'db.php');

require (BASE_DIR.FRONTEND_DIR.'db.php');
require (BASE_DIR.FRONTEND_DIR.'site.php');
require (BASE_DIR.FRONTEND_DIR.'session.php');
require (BASE_DIR.MODULE_DIR.'administrator/log/module.php');
require (BASE_DIR.INCLUDE_DIR.'error_handler.php');
require (BASE_DIR.INCLUDE_DIR.'autoloader.php');

try {
    require_once(BASE_DIR.FRONTEND_DIR.'bootstrap.php');
} catch (\Exception $e) {
    $log->log('System', 'Fatal error', $e->getMessage().' in '.$e->getFile().':'.$e->getLine());
    throw $e;
}
