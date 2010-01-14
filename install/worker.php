<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

ini_set('display_errors', '0');
	session_start();

if(isset($_POST['action']) && $_POST['action'] == 'create_database'){
  if (strlen($_POST['prefix']) > strlen('ip_cms_')) {
    echo "ERROR_LONG_PREFIX";
    exit;
  }
	$conn = mysql_connect($_POST['server'], $_POST['db_user'], $_POST['db_pass']);
	if(!$conn) {
		echo "ERROR_CONNECT";
	} else {
		if(mysql_select_db($_POST['db'], $conn)){
			mysql_query("SET NAMES utf8 COLLATE utf8_general_ci", $conn);
			mysql_query("SET CHARACTER SET utf8", $conn);
			/*structure*/
			$sqlFile = "sql/structure.sql";
			$fh = fopen($sqlFile, 'r');
			$all_sql = fread($fh, filesize($sqlFile));
			fclose($fh);			
			

			$all_sql = str_replace("[[[[database]]]]", $_POST['db'], $all_sql);
			$all_sql = str_replace("TABLE IF EXISTS `ip_cms_", "TABLE IF EXISTS `".$_POST['prefix'], $all_sql);
			$all_sql = str_replace("TABLE IF NOT EXISTS `ip_cms_", "TABLE IF NOT EXISTS `".$_POST['prefix'], $all_sql);
			$sql_list = explode("-- Table structure", $all_sql);
			
			$error = false;


			foreach($sql_list as $key => $sql){
				$rs = mysql_query($sql);
				if(!$rs){
					$error = true;
					echo mysql_error()." ";
        }
			}
			/*end structure*/
			
			/*data*/
			$sqlFile = "sql/data.sql";
			$fh = fopen($sqlFile, 'r');
			$all_sql = fread($fh, utf8_decode(filesize($sqlFile)));
			fclose($fh);			
			
			//$all_sql = utf8_encode($all_sql);
			$all_sql = str_replace("INSERT INTO `ip_cms_", "INSERT INTO `".$_POST['prefix'], $all_sql);
			$all_sql = str_replace("[[[[base_url]]]]", get_parent_url(), $all_sql);
			$sql_list = split("-- Dumping data for table--", $all_sql);

			
			foreach($sql_list as $key => $sql){
			/*	$semicolon_pos = strrpos($sql, ";");
				$sql = substr($sql, 0, $semicolon_pos);*/
				$rs = mysql_query($sql);
 				if(!$rs) {
					$error = true;
        }
			}

			/*end data*/

			if($error)
					echo "ERROR_QUERY";
					
		}else{
			echo "ERROR_DB";
		}
	}	
  mysql_close($conn);    
	if($error == false){
		if($_SESSION['step'] < 3)
			$_SESSION['step'] = 3;
			
		$_SESSION['db_server'] = $_POST['server'];
		$_SESSION['db_db'] = $_POST['db'];
		$_SESSION['db_user'] = $_POST['db_user'];
		$_SESSION['db_pass'] = $_POST['db_pass'];
		$_SESSION['db_prefix'] = $_POST['prefix'];
		
	}
}

if(isset($_POST['action']) && $_POST['action'] == 'config'){
  $errors = array();
	if($_POST['site_name'] == '')
		$errors[] = 'ERROR_SITE_NAME';

	if($_POST['site_email'] == '' || !preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $_POST['site_email']))
		$errors[] = 'ERROR_SITE_EMAIL';

  if(!isset($_POST['install_login']) || !isset($_POST['install_pass']) || $_POST['install_login'] == '' || $_POST['install_pass'] == '')
		$errors[] = 'ERROR_LOGIN';

	if(isset($_POST['timezone'])&& $_POST['timezone'] != '')
		$timezone = 'date_default_timezone_set(\''.$_POST['timezone'].'\');';
	else
		$errors[] = 'ERROR_TIME_ZONE';

	if($_POST['email'] != '' && !preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $_POST['email'])){
		$errors[] = 'ERROR_EMAIL';
	}
		
  if (sizeof($errors) > 0) {
    die(implode(" ", $errors));
  }

	$config = 
	"<?"."php
	
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

	if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
	// GLOBAL
	  define('SESSION_NAME', 'ses".rand()."');  //prevents session conflict when two sites runs on the same server
	// END GLOBAL

	// DB
	  define('DB_SERVER', '".$_SESSION['db_server']."'); // eg, localhost
	  define('DB_USERNAME', '".$_SESSION['db_user']."');
	  define('DB_PASSWORD', '".$_SESSION['db_pass']."');
	  define('DB_DATABASE', '".$_SESSION['db_db']."');
	  define('DB_PREF', '".$_SESSION['db_prefix']."');
	// END DB

	// GLOBAL
	  define('BASE_DIR', '".get_parent_dir()."'); //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
	  define('BASE_URL', '".get_parent_url()."'); //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
	  define('IMAGE_DIR', 'image/');  //uploaded images directory
	  define('TMP_IMAGE_DIR', 'image/tmp/'); //temporary images directory
	  define('IMAGE_REPOSITORY_DIR', 'image/repository/'); //images repository. Used for TinyMCE and others where user can browse the images.
	  define('FILE_DIR', 'file/'); //uploded files directory
	  define('TMP_FILE_DIR', 'file/tmp/'); //temporary files directory
	  define('FILE_REPOSITORY_DIR', 'file/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.
	  define('VIDEO_DIR', 'video/'); //uploaded video directory
	  define('TMP_VIDEO_DIR', 'video/tmp/'); //temporary video directory
	  define('VIDEO_REPOSITORY_DIR', 'video/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.
	  define('AUDIO_DIR', 'audio/'); //uploaded audio directory
	  define('TMP_AUDIO_DIR', 'audio/tmp/'); //temporary audio directory
	  define('AUDIO_REPOSITORY_DIR', 'audio/repository/'); //audio repository. Used for TinyMCE and others where user can browse the files.
	  
	  define('ERRORS_SHOW', 1);  //0 if you don't wish to display errors on the page
	  define('ERRORS_SEND', '".$_POST['email']."'); //insert email address or leave blank. If email is set, you will get an email when an error occurs.
	// END GLOBAL
	  
	// BACKEND
	  
	  define('INCLUDE_DIR', 'ip_cms/includes/'); //system directory
	  define('BACKEND_DIR', 'ip_cms/backend/'); //system directory
	  define('FRONTEND_DIR', 'ip_cms/frontend/'); //system directory
	  define('LIBRARY_DIR', 'ip_libs/'); //general classes and thrid party libraries
	  define('MODULE_DIR', 'ip_cms/modules/'); //system modules directory
	  define('CONFIG_DIR', 'ip_configs/'); //modules configuration directory
	  define('PLUGIN_DIR', 'ip_plugins/'); //plugins directory
	  define('THEME_DIR', 'ip_themes/'); //themes directory
	  
	  define('BACKEND_MAIN_FILE', 'admin.php'); //backend root file
	  define('BACKEND_WORKER_FILE', 'ip_backend_worker.php'); //backend worker root file

	// END BACKEND

	// FRONTEND

	  define('CHARSET', 'UTF-8'); //system characterset
	  define('MYSQL_CHARSET', 'utf8');
	  define('THEME', 'ip_default'); //theme from themes directory

	  mb_internal_encoding(CHARSET);  
	  ".$timezone."; //PHP 5 requires timezone to be set.

	// END FRONTEND  
";




	$myFile = "../ip_config.php";
	$fh = fopen($myFile, 'w') or die("ERROR_CONFIG");
	fwrite($fh, $config);
	fclose($fh);

	
	$robots = 
	'User-Agent: *
	Disallow: /ip_cms/
	Disallow: /ip_configs/
	Disallow: /update/
	Disallow: /install/
  Disallow: admin.php
	Disallow: ip_backend_frames.php
  Disallow: ip_backend_worker.php
	Disallow: ip_config.php
  Disallow: ip_cron.php
	Disallow: ip_license.html
  Disallow: readme.txt
	Sitemap: '.get_parent_url().'sitemap.php';

	$myFile = "../robots.txt";
	$fh = fopen($myFile, 'w') or die("ERROR_ROBOTS");
	fwrite($fh, $robots);
	fclose($fh);

	$conn = mysql_connect($_SESSION['db_server'], $_SESSION['db_user'], $_SESSION['db_pass']); 
	if(!$conn) {
		die("ERROR_CONNECT");
	} else {
		if(mysql_select_db($_SESSION['db_db'], $conn)){
			mysql_query("SET NAMES utf8 COLLATE utf8_general_ci", $conn);
			mysql_query("SET CHARACTER SET utf8", $conn);
	
	
	    //login and password
			$sql = "update ".$_SESSION['db_prefix']."user set pass = '".md5($_POST['install_pass'])."', name='".mysql_real_escape_string($_POST['install_login'])."' where 1 limit 1";
			$rs = mysql_query($sql);
			if(!$rs)
				die("ERROR_QUERY");
			
			//site name and email
			$sql = "update ".$_SESSION['db_prefix']."par_lang set `translation` = REPLACE(`translation`, '[[[[site_name]]]]', '".mysql_real_escape_string($_POST['site_name'])."') where 1";
			$rs = mysql_query($sql);
			if(!$rs)
				die("ERROR_QUERY");
			$sql = "update ".$_SESSION['db_prefix']."par_lang set `translation` = REPLACE(`translation`, '[[[[site_email]]]]', '".mysql_real_escape_string($_POST['site_email'])."') where 1";
			$rs = mysql_query($sql);
			if(!$rs)
				die("ERROR_QUERY");
			$sql = "update ".$_SESSION['db_prefix']."mc_misc_contact_form set `email_to` = REPLACE(`email_to`, '[[[[site_email]]]]', '".mysql_real_escape_string($_POST['site_email'])."') where 1";
			$rs = mysql_query($sql);
			if(!$rs)
				die("ERROR_QUERY");
			$rs = mysql_query($sql);
			if(!$rs)
				die("ERROR_QUERY");
			
			
		}else die("ERROR_DB");

		
		mysql_close($conn);
		
	}

	
	if ($_SESSION['step'] < 4) {
		$_SESSION['step'] = 4;
  }

}


function get_parent_url() {
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  
  $pageURL = substr($pageURL, 0, strrpos($pageURL, '/'));
  $pageURL = substr($pageURL, 0, strrpos($pageURL, '/') + 1);
  
  return $pageURL;
}

function get_parent_dir() {
  $dir = __DIR__;
  $dir = str_replace("\\", "/", $dir);
	$dir = substr($dir, 0, strrpos($dir, '/') + 1);
 
 return $dir;
}