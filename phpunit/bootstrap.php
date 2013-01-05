<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

require_once('./config.php');

session_start('ipUpdateTest');

require_once(TEST_BASE_DIR.'/Autoloader.php');
$autoloader = new \PhpUnit\Autoloader();
$autoloader->register(TEST_BASE_DIR);


//bootstrap IpUpdate library
require_once(CODEBASE_DIR.'update/Library/Bootstrap.php');
$libraryBootstrap = new \IpUpdate\Library\Bootstrap();
$libraryBootstrap->run();


