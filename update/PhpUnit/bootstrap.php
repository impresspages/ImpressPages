<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

define ('BASE_DIR', __DIR__.'/');
define ('UPDATE_DIR', __DIR__.'/../');
define ('INSTALLATION_DIR', __DIR__.'/../../');

define ('FIXTURE_DIR', BASE_DIR.'Fixture/');
define ('TMP_DIR', BASE_DIR.'Tmp/');

session_start('ipUpdateuTest');

//bootstrap IpUpbdate library
require_once(UPDATE_DIR.'/Library/Bootstrap.php');
$libraryBootstrap = new \IpUpdate\Library\Bootstrap();
$libraryBootstrap->run();

require_once('./UpdateTestCase.php');
