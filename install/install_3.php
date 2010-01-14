<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

if (INSTALL!="true") exit;
 
 
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
		$_SESSION['db_prefix'] = 'ip_cms_';

	
output('

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
				switch(response){
					case \'ERROR_CONNECT\':
						document.getElementById(\'errorConnect\').style.display = \'block\';
					break;
					case \'ERROR_DB\':
						document.getElementById(\'errorDb\').style.display = \'block\';
					break;
					case \'ERROR_QUERY\':
						document.getElementById(\'errorQuery\').style.display = \'block\';
					break;
					case \'ERROR_LONG_PREFIX\':
						document.getElementById(\'errorLongPrefix\').style.display = \'block\';
					break;
					default:
						document.getElementById(\'errorQuery\').style.display = \'block\';
					break;
				}
			
			} else {
				document.location= \'index.php?step=4\';
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
	document.getElementById(\'errorAllFields\').style.display = \'none\';
			document.getElementById(\'errorConnect\').style.display = \'none\';
			document.getElementById(\'errorDb\').style.display = \'none\';
			document.getElementById(\'errorQuery\').style.display = \'none\';
			document.getElementById(\'errorLongPrefix\').style.display = \'none\';


	var url = \'\';
	var server = document.getElementById(\'db_server\').value;
	var user = document.getElementById(\'db_user\').value;
	var pass = document.getElementById(\'db_pass\').value;
	var db = document.getElementById(\'db_db\').value;
	var prefix = document.getElementById(\'db_prefix\').value;
	if(server == \'\' ||  user == \'\' || db == \'\' || prefix == \'\'){
		document.getElementById(\'errorAllFields\').style.display = \'block\';
	}else{
		url = \'action=create_database&server=\' + escape(server) + \'&db_user=\' + escape(user) + \'&db_pass=\' + escape(pass) + \'&db=\' + escape(db) + \'&prefix=\' + escape(prefix); 
		ajaxMessage(\'worker.php\', url);
	}
}
</script>

<h1>'.IP_STEP_DB_LONG.'</h1>

<div id="errorAllFields" class="noDisplay"><p class="error">'.IP_DB_ERROR_ALL_FIELDS.'</p></div>
<div id="errorConnect" class="noDisplay"><p class="error">'.IP_DB_ERROR_CONNECT.'</p></div>
<div id="errorDb" class="noDisplay"><p class="error">'.IP_DB_ERROR_DB.'</p></div>
<div id="errorQuery" class="noDisplay"><p class="error">'.IP_DB_ERROR_QUERY.'</p></div>
<div id="errorLongPrefix" class="noDisplay"><p class="error">'.IP_DB_ERROR_LONG_PREFIX.'</p></div>
<form onsubmit="return false">
	<p><strong>'.IP_DB_SERVER.'</strong><input id="db_server" type="text" name="server" value="'.$_SESSION['db_server'].'"></p>
	<p><strong>'.IP_DB_USER.'</strong><input id="db_user" type="text" name="db_user" value="'.$_SESSION['db_user'].'"></p>
	<p><strong>'.IP_DB_PASS.'</strong><input id="db_pass" type="password" name="db_pass" value="'.$_SESSION['db_pass'].'"></p>
	<p><strong>'.IP_DB_DB.'</strong><input id="db_db" type="text" name="db" value="'.$_SESSION['db_db'].'"></p>
	<p><strong>'.IP_DB_PREFIX.'</strong><input id="db_prefix" maxlength="7" type="text" name="prefix" value="'.$_SESSION['db_prefix'].'"></p>
	
</form>
<p>'.IP_DB_DATA_WARNING.'</p>
<a class="button_act" href="javascript:void(0);" onclick="execute_ajax()">'.IP_NEXT.'</a>
<a class="button" href="?step=2">'.IP_BACK.'</a>

');



?>