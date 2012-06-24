<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');

require_once('Gui/Autoloader.php');
$autoloader = new Gui\Autoloader();
$autoloader->register(__DIR__.'/');


$bootstrap = new Gui\Bootstrap();
$bootstrap->run();

