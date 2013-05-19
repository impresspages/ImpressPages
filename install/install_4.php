<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


if (!defined('INSTALL')) exit;

$answer = '';


$dateTimeObject = new DateTime();
$currentTimeZone = $dateTimeObject->getTimezone()->getName();
$timezoneSelectOptions = '';

$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC);

$lastGroup = '';
foreach($timezones as $timezone) {
    $timezoneParts = explode('/', $timezone);
    $curGroup = $timezoneParts[0];
    if ($curGroup != $lastGroup) {
        if ($lastGroup != '') {
            $timezoneSelectOptions .= '</optgroup>';
        }
        $timezoneSelectOptions .= '<optgroup label="'.addslashes($curGroup).'">';
        $lastGroup = $curGroup;
    }
    if ($timezone == $currentTimeZone) {
        $selected = 'selected';
    } else {
        $selected = '';
    }
    $timezoneSelectOptions .= '<option '.$selected.' value="'.addslashes($timezone).'">'.htmlspecialchars($timezone).'</option>';
}


$answer .= '

<h1>'.IP_STEP_CONFIGURATION_LONG.'</h1>


<div id="errorSiteName" class="noDisplay"><p class="error">'.IP_CONFIG_ERROR_SITE_NAME.'</p></div>
<div id="errorSiteEmail" class="noDisplay"><p class="error">'.IP_CONFIG_ERROR_SITE_EMAIL.'</p></div>
<div id="errorLogin" class="noDisplay"><p class="error">'.IP_CONFIG_ERROR_LOGIN.'</p></div>
<div id="errorEmail" class="noDisplay"><p class="error">'.IP_CONFIG_ERROR_EMAIL.'</p></div>
<div id="errorConfig" class="noDisplay"><p class="error">'.IP_CONFIG_ERROR_CONFIG.'</p></div>
<div id="errorRobots" class="noDisplay"><p class="error">'.IP_CONFIG_ERROR_ROBOTS.'</p></div>
<div id="errorTimeZone" class="noDisplay"><p class="error">'.IP_CONFIG_ERROR_TIME_ZONE.'</p></div>
<div id="errorConnect" class="noDisplay"><p class="error">'.IP_DB_ERROR_CONNECT.'</p></div>
<div id="errorDb" class="noDisplay"><p class="error">'.IP_DB_ERROR_DB.'</p></div>
<div id="errorQuery" class="noDisplay"><p class="error">'.IP_DB_ERROR_QUERY.'</p></div>
<form onsubmit="return false;">
	<p><strong>'.IP_CONFIG_SITE_NAME.'</strong><input id="config_site_name" type="text" name="site_name"></p>
	<p><strong>'.IP_CONFIG_SITE_EMAIL.'</strong><input id="config_site_email" type="text" name="site_email"></p>
	<p><strong>'.IP_CONFIG_LOGIN.'</strong><input id="config_login" type="text" name="install_login"></p>
	<p><strong>'.IP_CONFIG_PASS.'</strong><input id="config_pass" type="password" name="install_pass"></p>
	<p><strong>'.IP_CONFIG_EMAIL.'</strong><input id="config_email" type="text" name="email" ></p>
	<p><strong>'.IP_CONFIG_TIMEZONE.'</strong>
	<select id="config_timezone" name="config_timezone">
		'.$timezoneSelectOptions.'

	</select>
	</p>
	
</form>
<a class="button_act" href="#" >'.IP_NEXT.'</a>
<a class="button" href="?step=3">'.IP_BACK.'</a>
';

output($answer, array('js/step4.js'));

?>