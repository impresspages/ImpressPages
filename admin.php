<?php
/**
 * ImpressPages CMS main backend file
 *
 * This file initiates required variables and outputs backend content.
 *
 * @package ImpressPages
 *
 *
 */

namespace Backend;


if (!defined('CMS')) {
    define('CMS', true); // make sure other files are accessed through this file.
}
if (!defined('BACKEND')) {
    define('BACKEND', true); // make sure other files are accessed through this file.
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
require (BASE_DIR.FRONTEND_DIR.'site.php');
require (BASE_DIR.MODULE_DIR.'administrator/log/module.php');
require (BASE_DIR.INCLUDE_DIR.'error_handler.php');
require (BASE_DIR.INCLUDE_DIR.'autoloader.php');
require (BASE_DIR.BACKEND_DIR.'cms.php');
require (BASE_DIR.BACKEND_DIR.'db.php');

$dispatcher = new \Ip\Dispatcher();


$parametersMod = new \ParametersMod();


if(\Db::connect()){


    $log = new \Modules\Administrator\Log\Module();

    $site = new \Site(); /*to generate links to site and get other data about frontend*/

    $site->init();



    $cms = new Cms();

    $cms->makeActions();

    $cms->manage();


    \Db::disconnect();
}else   trigger_error('Database access');

