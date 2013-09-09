<?php
/**
 * @package ImpressPages
 *
 *
 */


function install_available(){
    if(filesize("../ip_config.php") !== false && filesize("../ip_config.php") < 100)
    return true;
    else
    return false;
}


if(!install_available()) {
    return;
}




ini_set('display_errors', '0');
session_start();

if(isset($_POST['action']) && $_POST['action'] == 'sessionSetTest'){
    $_SESSION['test'] = 'value';
    echo '{"status":"success"}';
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'sessionGetTest'){
    if (isset($_SESSION['test']) && $_SESSION['test'] == 'value') {
        echo '{"status":"success"}';
        exit;
    } else {
        echo '{"status":"error"}';
        exit;
    }
}


if(isset($_POST['action']) && $_POST['action'] == 'create_database'){
    if (strlen($_POST['prefix']) > strlen('ip_cms_')) {
        echo '{errorCode:"ERROR_LONG_PREFIX", error:""}';
        exit;
    }

    if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)$/', $_POST['prefix'])) {
        echo '{errorCode:"ERROR_INCORRECT_PREFIX", error:""}';
        exit;
    }

    $error = false;
    $conn = mysql_connect($_POST['server'], $_POST['db_user'], $_POST['db_pass']);
    if(!$conn) {
        $error = true;
        echo '{errorCode:"ERROR_CONNECT", error:""}';
        exit;
    } else {
        if(mysql_select_db($_POST['db'], $conn)){
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
             
            $errorMessage = '';


            foreach($sql_list as $key => $sql){
                $rs = mysql_query($sql);
                if(!$rs){
                    $error = true;
                    $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
                    echo $errorMessage;
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
            $sql_list = explode("-- Dumping data for table--", $all_sql);

             
            foreach($sql_list as $key => $sql){
                $rs = mysql_query($sql);
                if(!$rs) {
                    $error = true;
                    $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
                }
            }

            /*end data*/
            
            define('BASE_DIR', get_parent_dir());
            define('BACKEND', 1);
            define('CMS', 1);
            define('INCLUDE_DIR', 'ip_cms/includes/');
            define('FRONTEND_DIR', 'ip_cms/frontend/');
            define('MODULE_DIR', 'ip_cms/modules/');
            define('LIBRARY_DIR', 'ip_libs/');
            define('DB_PREF', $_POST['prefix']);
            define('THEME', 'Blank');
            define('THEME_DIR', 'ip_themes/');
            

            require (BASE_DIR.FRONTEND_DIR.'db.php');
            require (BASE_DIR.INCLUDE_DIR.'db.php');
            require (BASE_DIR.INCLUDE_DIR.'parameters.php');
            require (__DIR__.'/themeParameters.php');
            require_once(BASE_DIR.'ip_cms/modules/developer/localization/manager.php');

            global $parametersMod;
            $parametersMod = new parametersMod();
            
            
            \Modules\developer\localization\Manager::saveParameters(__DIR__.'/parameters.php');
            
            \Modules\developer\localization\Manager::saveParameters(__DIR__.'/themeParameters.php');
            
            if($error) {
                echo '{errorCode:"ERROR_QUERY", error:"'.addslashes($errorMessage).'"}';
            }
             
        }else{
            $error = true;
            echo '{errorCode:"ERROR_DB", error:""}';
            exit;
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
    $timezone = 'date_default_timezone_set(\''.$_POST['timezone'].'\')';
    else
    $errors[] = 'ERROR_TIME_ZONE';

    if($_POST['email'] != '' && !preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $_POST['email'])){
        $errors[] = 'ERROR_EMAIL';
    }

    if (sizeof($errors) > 0) {
        die('{errorCode:"'.implode(" ", $errors).'", error:""}');
    }


    $config =
	"<"."?php
	
/**
 * @package ImpressPages
 *
 *
 */
    
    if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
    // GLOBAL
      define('SESSION_NAME', 'ses".rand()."');  //prevents session conflict when two sites runs on the same server
    // END GLOBAL
    
    // DB
      define('DB_SERVER', '".addslashes($_SESSION['db_server'])."'); // eg, localhost
      define('DB_USERNAME', '".addslashes($_SESSION['db_user'])."');
      define('DB_PASSWORD', '".addslashes($_SESSION['db_pass'])."');
      define('DB_DATABASE', '".addslashes($_SESSION['db_db'])."');
      define('DB_PREF', '".addslashes($_SESSION['db_prefix'])."');
    // END DB
    
    // GLOBAL
      define('BASE_DIR', '".get_parent_dir()."'); //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
      define('BASE_URL', '".get_parent_url()."'); //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
      define('IMAGE_DIR', 'image/');  //uploaded images directory
      define('TMP_IMAGE_DIR', 'image/tmp/'); //temporary images directory
      define('IMAGE_REPOSITORY_DIR', 'image/repository/'); //images repository. Used for TinyMCE and others where user can browse the images.
      define('FILE_DIR', 'file/'); //uploded files directory
      define('TMP_FILE_DIR', 'file/tmp/'); //temporary files directory
      define('FILE_REPOSITORY_DIR', 'file/repository/'); //files repository.
      define('SECURE_DIR', 'file/secure/'); //directory not accessible from the Internet
      define('TMP_SECURE_DIR', 'file/secure/tmp/'); //directory for temporary files. Not accessible from the Internet.
      define('MANUAL_DIR', 'file/manual/'); //Used for TinyMCE file browser and others tools where user manually controls all files.
      define('VIDEO_DIR', 'video/'); //uploaded video directory
      define('TMP_VIDEO_DIR', 'video/tmp/'); //temporary video directory
      define('VIDEO_REPOSITORY_DIR', 'video/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.
      define('AUDIO_DIR', 'audio/'); //uploaded audio directory
      define('TMP_AUDIO_DIR', 'audio/tmp/'); //temporary audio directory
      define('AUDIO_REPOSITORY_DIR', 'audio/repository/'); //audio repository. Used for TinyMCE and others where user can browse the files.
      
      define('DEVELOPMENT_ENVIRONMENT', 1); //displays error and debug information. Change to 0 before deployment to production server
      define('ERRORS_SHOW', 1);  //0 if you don't wish to display errors on the page
      define('ERRORS_SEND', '".$_POST['email']."'); //insert email address or leave blank. If email is set, you will get an email when an error occurs.
    // END GLOBAL
      
    // BACKEND
      
      define('INCLUDE_DIR', 'ip_cms/includes/'); //system directory
      define('BACKEND_DIR', 'ip_cms/backend/'); //system directory
      define('FRONTEND_DIR', 'ip_cms/frontend/'); //system directory
      define('LIBRARY_DIR', 'ip_libs/'); //general classes and third party libraries
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
      define('THEME', 'Blank'); //theme from themes directory
      define('DEFAULT_DOCTYPE', 'DOCTYPE_HTML5'); //look ip_cms/includes/Ip/View.php for available options.
    
      mb_internal_encoding(CHARSET);  
      ".$timezone."; //PHP 5 requires timezone to be set.
    
    // END FRONTEND  
";



    $myFile = "../ip_config.php";
    $fh = fopen($myFile, 'w') or die('{errorCode:"ERROR_CONFIG", error:""}');
    fwrite($fh, $config);
    fclose($fh);


    $robots =
'User-agent: *
Disallow: /ip_cms/
Disallow: /ip_configs/
Disallow: /update/
Disallow: /install/
Disallow: /admin.php
Disallow: /ip_backend_frames.php
Disallow: /ip_backend_worker.php
Disallow: /ip_config.php
Disallow: /ip_cron.php
Disallow: /ip_license.html
Disallow: /readme.txt
Sitemap: '.get_parent_url().'sitemap.php';

    $myFile = "../robots.txt";
    $fh = fopen($myFile, 'w') or die('{errorCode:"ERROR_ROBOTS", error:""}');
    fwrite($fh, $robots);
    fclose($fh);

    $conn = mysql_connect($_SESSION['db_server'], $_SESSION['db_user'], $_SESSION['db_pass']);
    if(!$conn) {
        die('{errorCode:"ERROR_CONNECT", error:""}');
    } else {
        if(mysql_select_db($_SESSION['db_db'], $conn)){
            mysql_query("SET CHARACTER SET utf8", $conn);


            //login and password
            $sql = "update `".$_SESSION['db_prefix']."user` set pass = '".md5($_POST['install_pass'])."', name='".mysql_real_escape_string($_POST['install_login'])."' where 1 limit 1";
            $rs = mysql_query($sql);
            if(!$rs){
                $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
                die('{errorCode:"ERROR_QUERY", error:"'.addslashes($errorMessage).'"}');
            }
            //site name and email
            $sql = "update `".$_SESSION['db_prefix']."par_lang` set `translation` = REPLACE(`translation`, '[[[[site_name]]]]', '".mysql_real_escape_string($_POST['site_name'])."') where 1";
            $rs = mysql_query($sql);
            if(!$rs){
                $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
                die('{errorCode:"ERROR_QUERY", error:"'.addslashes($errorMessage).'"}');
            }
            $sql = "update `".$_SESSION['db_prefix']."par_lang` set `translation` = REPLACE(`translation`, '[[[[site_email]]]]', '".mysql_real_escape_string($_POST['site_email'])."') where 1";
            $rs = mysql_query($sql);
            if(!$rs){
                $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
                die('{errorCode:"ERROR_QUERY", error:"'.addslashes($errorMessage).'"}');
            }
            /*TODO follow the new structure
             *             $sql = "update `".$_SESSION['db_prefix']."mc_misc_contact_form` set `email_to` = REPLACE(`email_to`, '[[[[site_email]]]]', '".mysql_real_escape_string($_POST['site_email'])."') where 1";
             $rs = mysql_query($sql);
             if(!$rs){
             $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
             die('{errorCode:"ERROR_QUERY", error:"'.addslashes($errorMessage).'"}');
             }*/
            $rs = mysql_query($sql);
            if(!$rs){
                $errorMessage = preg_replace("/[\n\r]/","",$sql.' '.mysql_error());
                die('{errorCode:"ERROR_QUERY", error:"'.addslashes($errorMessage).'"}');
            }
             
             
        }else die('{errorCode:"ERROR_DB", error:""}');


        mysql_close($conn);

    }


    if ($_SESSION['step'] < 4) {
        $_SESSION['step'] = 4;
    }

}


function get_parent_url() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
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

if (isset($_POST['manual'])) {
    echo '{errorCode:"ERROR_OK", error:""}';
} else {
    //keeping compatability for installatron and softaculous
}