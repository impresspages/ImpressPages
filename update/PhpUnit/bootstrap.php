<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */



session_start('ipUpdateuTest');

require_once(BASE_DIR.'/Autoloader.php');
$autoloader = new \IpUpdate\PhpUnit\Autoloader();
$autoloader->register(BASE_DIR);


//bootstrap IpUpbdate library
require_once(UPDATE_DIR.'/Library/Bootstrap.php');
$libraryBootstrap = new \IpUpdate\Library\Bootstrap();
$libraryBootstrap->run();


