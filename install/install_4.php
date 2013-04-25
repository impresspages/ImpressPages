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

<script type="text/javascript">
function ajaxMessage(url, parameters){
	var xmlHttp;
	try	{// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}catch (e){// Internet Explorer
		try{  
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}catch (e){
			try{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e){
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	xmlHttp.onreadystatechange=function()
	{
		
		if(xmlHttp.readyState==4){
			var response = xmlHttp.responseText;			
			if(response != \'\'){
  			var responseObject = eval(\'(\' + response + \')\');
  			var responseString = responseObject.errorCode;
  			var responseArray = responseString.split(\' \');
			
  			for (var i in responseArray) {
  			 var response = responseArray[i];
  				switch(response){
  					case \'ERROR_SITE_NAME\':
  						document.getElementById(\'errorSiteName\').style.display = \'block\';
  					break;
  					case \'ERROR_SITE_EMAIL\':
  						document.getElementById(\'errorSiteEmail\').style.display = \'block\';
  					break;
  					case \'ERROR_EMAIL\':
  						document.getElementById(\'errorEmail\').style.display = \'block\';
  					break;
  					case \'ERROR_CONFIG\':
  						document.getElementById(\'errorConfig\').style.display = \'block\';
  					break;
  					case \'ERROR_ROBOTS\':
  						document.getElementById(\'errorRobots\').style.display = \'block\';
  					break;
  					case \'ERROR_CONNECT\':
  						document.getElementById(\'errorConnect\').style.display = \'block\';
  					break;
  					case \'ERROR_DB\':
  						document.getElementById(\'errorDb\').style.display = \'block\';
  					break;
  					case \'ERROR_QUERY\':
              var textNode = document.createTextNode(responseObject.error);
              document.getElementById(\'errorQuery\').innerHTML = \'\';
              document.getElementById(\'errorQuery\').appendChild(textNode);
              document.getElementById(\'errorQuery\').innerHTML = \'<p class="error">\' + document.getElementById(\'errorQuery\').innerHTML + \'</p>\';
  						document.getElementById(\'errorQuery\').style.display = \'block\';
  					break;
  					case \'ERROR_LOGIN\':
  						document.getElementById(\'errorLogin\').style.display = \'block\';
  					break;
  					case \'ERROR_TIME_ZONE\':
  						document.getElementById(\'errorTimeZone\').style.display = \'block\';						
  					break;
  				}
  			
  			}			
      }  			
			
      if (response == \'\') {
				document.location= \'index.php?step=5\';
		  }
		}
	}

	xmlHttp.open("POST",url, true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", parameters.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(parameters);
}


function execute_ajax(){
	document.getElementById(\'errorSiteName\').style.display = \'none\';
	document.getElementById(\'errorSiteEmail\').style.display = \'none\';
	document.getElementById(\'errorEmail\').style.display = \'none\';
	document.getElementById(\'errorConfig\').style.display = \'none\';
	document.getElementById(\'errorRobots\').style.display = \'none\';
	document.getElementById(\'errorConnect\').style.display = \'none\';
	document.getElementById(\'errorDb\').style.display = \'none\';
	document.getElementById(\'errorQuery\').style.display = \'none\';
	document.getElementById(\'errorLogin\').style.display = \'none\';
	document.getElementById(\'errorTimeZone\').style.display = \'none\';


	var url = \'\';
	var site_name = document.getElementById(\'config_site_name\').value;
	var site_email = document.getElementById(\'config_site_email\').value;
	var login = document.getElementById(\'config_login\').value;
	var pass = document.getElementById(\'config_pass\').value;
	var email = document.getElementById(\'config_email\').value;
	var timezone = document.getElementById(\'config_timezone\').value;
	{
		url = \'action=config&install_login=\' + encodeURIComponent(login) + \'&install_pass=\' + encodeURIComponent(pass) + \'&email=\' + encodeURIComponent(email) + \'&timezone=\' + encodeURIComponent(timezone) +\'&site_name=\' + encodeURIComponent(site_name) + \'&site_email=\' + encodeURIComponent(site_email); 
		ajaxMessage(\'worker.php\', url);
	}
}
</script>
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
<a class="button_act" href="javascript:void(0);" onclick="execute_ajax()">'.IP_NEXT.'</a>
<a class="button" href="?step=3">'.IP_BACK.'</a>
';

output($answer);

?>