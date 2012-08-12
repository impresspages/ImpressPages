<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

require_once('./config.php');

session_start('ipUpdateuTest');

require_once(TEST_BASE_DIR.'/Autoloader.php');
$autoloader = new \IpUpdate\PhpUnit\Autoloader();
$autoloader->register(TEST_BASE_DIR);


//bootstrap IpUpdate library
require_once(TEST_UPDATE_DIR.'/Library/Bootstrap.php');
$libraryBootstrap = new \IpUpdate\Library\Bootstrap();
$libraryBootstrap->run();


