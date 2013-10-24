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
        echo '{errorCode:"ERROR_CONNECT", error:""}';
        exit;
    }

    {
        if(!mysql_select_db($_POST['db'], $conn)){
            //try to create
            $rs = mysql_query("CREATE DATABASE `".$_POST['db']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
            if (!$rs) {
                echo '{errorCode:"ERROR_DB", error:""}';
                exit;
            }

            if(!mysql_select_db($_POST['db'], $conn)){
                $error = true;
                echo '{errorCode:"ERROR_DB", error:""}';
                exit;
            }
        }


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
            define('MODULE_DIR', 'ip_cms/modules/');
            define('LIBRARY_DIR', 'ip_libs/');
            define('DB_PREF', $_POST['prefix']);
            define('THEME', 'Blank');
            define('THEME_DIR', 'ip_themes/');
            

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
    $timezone = $_POST['timezone'];
    else
    $errors[] = 'ERROR_TIME_ZONE';

    if($_POST['email'] != '' && !preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $_POST['email'])){
        $errors[] = 'ERROR_EMAIL';
    }

    if (sizeof($errors) > 0) {
        die('{errorCode:"'.implode(" ", $errors).'", error:""}');
    }

    $configInfo = array(
        // GLOBAL
        'SESSION_NAME' => array(
            'value' => 'ses' . rand(),
            'comment' => 'prevents session conflict when two sites runs on the same server',
        ),
        'BASE_DIR' => array(
            'value' => get_parent_dir(),
             'comment' => 'root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.',
        ),
        'CORE_DIR' => array(
            'value' => '',
            'comment' => 'Directory where Ip directory resides',
        ),
        'BASE_URL' => array(
            'value' => get_parent_url(),
            'comment' => 'root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.',
        ),
        'FILE_DIR' => array(
            'value' => 'file/',
            'comment' => 'uploded files directory',
        ),
        'TMP_FILE_DIR' => array(
            'value' => 'file/tmp/',
            'comment' => 'temporary files directory',
        ),
        'FILE_REPOSITORY_DIR' => array(
            'value' => 'file/repository/',
            'comment' => 'files repository.',
        ),
        'SECURE_DIR' => array(
            'value' => 'file/secure/',
            'comment' => 'directory not accessible from the Internet',
        ),
        'TMP_SECURE_DIR' => array(
            'value' => 'file/secure/tmp/',
            'comment' => 'directory for temporary files. Not accessible from the Internet.',
        ),
        'MANUAL_DIR' => array(
            'value' => 'file/manual/',
            'comment' => 'Used for TinyMCE file browser and others tools where user manually controls all files.',
        ),
        'DEVELOPMENT_ENVIRONMENT' => array(
            'value' => 1,
            'comment' => 'displays error and debug information. Change to 0 before deployment to production server',
        ),
        'ERRORS_SHOW' => array(
            'value' => 1,
            'comment' => "0 if you don't wish to display errors on the page",
        ),
        'ERRORS_SEND' => array(
            'value' => $_POST['email'],
            'comment' => 'insert email address or leave blank. If email is set, you will get an email when an error occurs.',
        ),
        // END GLOBAL

        // BACKEND
        'INCLUDE_DIR' => array(
            'value' => 'ip_cms/includes/',
            'comment' => 'system directory',
        ),
        'LIBRARY_DIR' => array(
            'value' => 'ip_libs/',
            'comment' => 'general classes and third party libraries',
        ),
        'MODULE_DIR' => array(
            'value' => 'ip_cms/modules/',
            'comment' => 'system modules directory',
        ),
        'CONFIG_DIR' => array(
            'value' => 'ip_configs/',
            'comment' => 'modules configuration directory',
        ),
        'PLUGIN_DIR' => array(
            'value' => 'ip_plugins/',
            'comment' => 'plugins directory',
        ),
        'THEME_DIR' => array(
            'value' => 'ip_themes/',
            'comment' => 'themes directory',
        ),
        'BACKEND_MAIN_FILE' => array(
            'value' => 'admin.php',
            'comment' => 'backend root file',
        ),
        'BACKEND_WORKER_FILE' => array(
            'value' => 'ip_backend_worker.php',
            'comment' => 'backend worker root file'
        ),
    // END BACKEND

    // FRONTEND
        'CHARSET' => array(
            'value' => 'UTF-8',
            'comment' => 'system characterset',
        ),
        'THEME' => array(
            'value' => 'Blank',
            'comment' => 'theme from themes directory',
        ),
        'DEFAULT_DOCTYPE' => array(
            'value' => 'DOCTYPE_HTML5',
            'comment' => 'look ip_cms/includes/Ip/View.php for available options.'
        ),
        'timezone' => array(
            'value' => $timezone,
            'comment' => 'PHP 5 requires timezone to be set.',
        ),
        // DB
        'db' => array(
            'value' => array(
                'hostname' => $_SESSION['db_server'],
                'username' => $_SESSION['db_user'],
                'password' => $_SESSION['db_pass'],
                'database' => $_SESSION['db_db'],
                'tablePrefix' => $_SESSION['db_prefix'],
                'charset' => 'utf8',
            ),
            'comment' => 'Database configuration',
        ),
        'DB_PREF' => array(
            'value' => $_SESSION['db_prefix'],
        ),
        // END DB
    );

    $config = "";

    foreach ($configInfo as $key => $info) {
        $config.= "\n    '{$key}' => " . var_export($info['value'], true) . ",";
        if (!empty($info['comment'])) {
            $config.= " // " . $info['comment'];
        }
    }

    $config = "<"."?php

/**
 * @package ImpressPages
 */

 return array(" . $config . ");";

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
Disallow: /readme.md
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