<?php
/**
 *
 * ImpressPages CMS main frontend file
 * 
 * This file iniciates required variables and outputs the content.
 * 
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

/** Make sure files are accessed through index. */

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');

define('CMS', true); // make sure other files are accessed through this file.
define('BACKEND', true); // make sure other files are accessed through this file.
define('UPDATE_INCLUDE_DIR', 'includes/');


if (get_magic_quotes_gpc()) { //fix magic quotes option
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

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



