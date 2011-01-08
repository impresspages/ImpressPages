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

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');

define('CMS', true); // make sure other files are accessed through this file.
define('BACKEND', true); // make sure other files are accessed through this file.

define('UPDATE_INCLUDE_DIR', 'includes/');

require_once(UPDATE_INCLUDE_DIR.'scripts.php');
require_once(UPDATE_INCLUDE_DIR.'update.php');
require_once(UPDATE_INCLUDE_DIR.'navigation.php');
require_once(UPDATE_INCLUDE_DIR.'translations.php');
require_once(UPDATE_INCLUDE_DIR.'html_output.php');


$navigation = new Navigation ();
$scripts = new Scripts ();
$update = new Update ();
$htmlOutput = new HtmlOutput ();


$update->execute();



