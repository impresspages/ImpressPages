<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');

require_once('Library/Autoloader.php');
spl_autoload_register('__impressPagesUpdateAutoloader');

$bootstrap = new Gui\Bootstrap();
$bootstrap->run();

