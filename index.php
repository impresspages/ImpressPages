<?php
/**
 *
 * ImpressPages CMS main frontend file
 * 
 * This file iniciates required variables and outputs the content.
 * 
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

/** Make sure files are accessed through index. */
       

define('CMS', true); // make sure other files are accessed through this file.
define('FRONTEND', true); // make sure other files are accessed through this file.


error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');

if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
  echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages CMS you need PHP 5.3.*';
  exit;
}


if(is_file(__DIR__.'/ip_config.php')) {
  require (__DIR__.'/ip_config.php');
} else {
  require (__DIR__.'/../ip_config.php');
}


require (BASE_DIR.INCLUDE_DIR.'parameters.php');
require (BASE_DIR.INCLUDE_DIR.'db.php');

require (BASE_DIR.FRONTEND_DIR.'db.php');
require (BASE_DIR.FRONTEND_DIR.'site.php');
require (BASE_DIR.FRONTEND_DIR.'session.php');
require (BASE_DIR.MODULE_DIR.'administrator/log/module.php'); 
require (BASE_DIR.INCLUDE_DIR.'error_handler.php');
     
require_once(BASE_DIR.FRONTEND_DIR.'bootstrap.php');
