<?php
define('IP_VERSION', '3.9');

require (BASE_DIR.INCLUDE_DIR.'parameters.php');
require (BASE_DIR.INCLUDE_DIR.'db.php');

require (BASE_DIR.FRONTEND_DIR.'db.php');
require (BASE_DIR.FRONTEND_DIR.'site.php');
require (BASE_DIR.FRONTEND_DIR.'session.php');
require (BASE_DIR.MODULE_DIR.'administrator/log/module.php');
require (BASE_DIR.INCLUDE_DIR.'error_handler.php');
require (BASE_DIR.INCLUDE_DIR.'autoloader.php');

if(!Db::connect()){
    trigger_error("Database access");
}

global $log;
$log = new Modules\Administrator\Log\Module();
global $dispatcher;
$dispatcher = new \Ip\Dispatcher();
global $parametersMod;
$parametersMod = new parametersMod();
global $session;
$session = new Frontend\Session();
global $site;
$site = new \Site();