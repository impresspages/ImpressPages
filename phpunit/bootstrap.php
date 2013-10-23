<?php

/**
 * @package ImpressPages
 *
 *
 */

require_once('./config.php');

if (!session_name()) {
    session_start('ipUpdateTest');
}

require_once(TEST_BASE_DIR.'/Autoloader.php');
$autoloader = new \PhpUnit\Autoloader();
$autoloader->register(TEST_BASE_DIR);

require_once TEST_BASE_DIR . TEST_CODEBASE_DIR . 'Ip/Config.php';

//bootstrap core

//define('CMS', true);
//define('FRONTEND', true);
//define('BACKEND', true);
//require_once(TEST_CODEBASE_DIR.'ip_config.php');
//require (BASE_DIR.INCLUDE_DIR.'autoloader.php');
////end bootstrap core


//bootstrap IpUpdate library
require_once(TEST_CODEBASE_DIR.'update/Library/Bootstrap.php');
$libraryBootstrap = new \IpUpdate\Library\Bootstrap();
$libraryBootstrap->run();
//end IpUpdate bootstrap
