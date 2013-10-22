<?php
/**
 * @package ImpressPages
 *
 *
 */

if (!defined('INSTALL')) exit;


$licenseFile = "../ip_license.html";
$fh = fopen($licenseFile, 'r');
$license = fread($fh, filesize($licenseFile));
fclose($fh);


if(!isset($_SESSION['db_server']))
$_SESSION['db_server'] = 'localhost';

if(!isset($_SESSION['db_user']))
$_SESSION['db_user'] = '';

if(!isset($_SESSION['db_pass']))
$_SESSION['db_pass'] = '';

if(!isset($_SESSION['db_db']))
$_SESSION['db_db'] = '';

if(!isset($_SESSION['db_prefix']))
$_SESSION['db_prefix'] = 'ip_';


output('
<h1>'.IP_STEP_DB_LONG.'</h1>

<div id="errorAllFields" class="noDisplay"><p class="error">'.IP_DB_ERROR_ALL_FIELDS.'</p></div>
<div id="errorConnect" class="noDisplay"><p class="error">'.IP_DB_ERROR_CONNECT.'</p></div>
<div id="errorDb" class="noDisplay"><p class="error">'.IP_DB_ERROR_DB.'</p></div>
<div id="errorQuery" class="noDisplay"><p class="error">'.IP_DB_ERROR_QUERY.'</p></div>
<div id="errorLongPrefix" class="noDisplay"><p class="error">'.IP_DB_ERROR_LONG_PREFIX.'</p></div>
<div id="errorIncorrectPrefix" class="noDisplay"><p class="error">'.IP_DB_ERROR_INCORRECT_PREFIX.'</p></div>
<form onsubmit="return false">
	<p><strong>'.IP_DB_SERVER.'</strong><input id="db_server" type="text" name="server" value="'.$_SESSION['db_server'].'"></p>
	<p><strong>'.IP_DB_USER.'</strong><input id="db_user" type="text" name="db_user" value="'.$_SESSION['db_user'].'"></p>
	<p><strong>'.IP_DB_PASS.'</strong><input id="db_pass" type="password" name="db_pass" value="'.$_SESSION['db_pass'].'"></p>
	<p><strong>'.IP_DB_DB.'</strong><input id="db_db" type="text" name="db" value="'.$_SESSION['db_db'].'"></p>
	<p><strong>'.IP_DB_PREFIX.'</strong><input id="db_prefix" maxlength="7" type="text" name="prefix" value="'.$_SESSION['db_prefix'].'"></p>
	
</form>
<p>'.IP_DB_DATA_WARNING.'</p>
<a class="button_act" href="#">'.IP_NEXT.'</a>
<a class="button" href="?step=2">'.IP_BACK.'</a>

',
array('js/step3.js'));



?>