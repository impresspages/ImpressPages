<?php
/**
 * @package ImpressPages
 *
 * @license see license.html
 */
if (!defined('INSTALL')) exit;

complete_step(1);

function directory_is_writable($dir) {
    $answer = true;
    if(!is_writable($dir)) {
        $answer = false;
    }

    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if($file != ".." && !is_writable($dir.'/'.$file)) {
                $answer = false;
            }
        }
        closedir($handle);
    }

    return $answer;
}

$error = array();
$warning = array();

if(PHP_MAJOR_VERSION < 5)
$error['php_version'] = 1;
if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)
$error['php_version'] = 1;

if(function_exists('apache_get_modules') ) {
    if(!in_array('mod_rewrite',apache_get_modules()))
    $error['mod_rewrite'] = 1;
}

if(function_exists('apache_get_modules') ) {
    if(!in_array('mod_rewrite',apache_get_modules()))
    $error['mod_rewrite'] = 1;
}

if(!class_exists('PDO')) {
    $error['mod_pdo'] = 1;
}

if (!file_exists('./../.htaccess')) {
    $error['htaccess'] = 1;
}

if (file_exists('./../index.html')) {
    $error['index.html'] = 1;
}

if (!extension_loaded('gd') || !function_exists('gd_info')) {
    $error['gd_lib'] = 1;
}

if(get_magic_quotes_gpc()){
    $warning['magic_quotes'] = 1;
}


if (!function_exists('curl_init')) {
    $warning['curl'] = 1;
}


if (function_exists('curl_init')) {
    if (session_id() == '') { //session hasn't been started
        $warning['session'] = 1;
    }
}



$answer = '';
$answer = '<h1>'.IP_STEP_CHECK_LONG."</h1>";

$table = array();

$table[] = IP_PHP_VERSION;
if(isset($error['php_version']))
$table[] = '<span class="error">'.IP_ERROR."</span>";
else
$table[] = '<span class="correct">'.IP_OK.'</span>';

$table[] = IP_MOD_REWRITE;
if(isset($error['mod_rewrite']))
$table[] = '<span class="error">'.IP_ERROR."</span>";
else
$table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = IP_MOD_PDO;
if(isset($error['mod_pdo']))
    $table[] = '<span class="error">'.IP_ERROR."</span>";
else
    $table[] = '<span class="correct">'.IP_OK.'</span>';

$table[] = IP_GD_LIB;
if(isset($error['gd_lib']))
$table[] = '<span class="error">'.IP_ERROR."</span>";
else
$table[] = '<span class="correct">'.IP_OK.'</span>';

//sessions are checked using curl. If there is no curl, session availability hasn't been checked
if (!isset($warning['curl'])) {
    $table[] = IP_SESSION;
    if(isset($warning['session'])) {
        $table[] = '<span class="error">'.IP_ERROR."</span>";
    } else {
        $table[] = '<span class="correct">'.IP_OK.'</span>';
    }
}

$table[] = IP_HTACCESS;
if(isset($error['htaccess']))
    $table[] = '<span class="error">'.IP_ERROR."</span>";
else
    $table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = IP_INDEX_HTML;
if(isset($error['index.html']))
    $table[] = '<span class="error">'.IP_ERROR."</span>";
else
    $table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = IP_MAGIC_QUOTES;
if(isset($warning['magic_quotes']))
$table[] = '<span class="error">'.IP_ERROR."</span>";
else
$table[] = '<span class="correct">'.IP_OK.'</span>';

$table[] = IP_CURL;
if(isset($warning['curl'])) {
    $table[] = '<span class="warning">'.IP_WARNING."</span>";
} else {
    $table[] = '<span class="correct">'.IP_OK.'</span>';
}

    $table[] = str_replace('[[memory_limit]]', ini_get('memory_limit'), IP_MEMORY_LIMIT);
    if ((integer) ini_get('memory_limit') < 100){
        $table[] = '<span class="warning">'.IP_WARNING."</span>";
    } else {
        $table[] = '<span class="correct">'.IP_OK."</span>";
    }





$table[] = '';
$table[] = '';


$table[] = '';
$table[] = '';


$table[] = '<b>/audio/</b> '.IP_WRITABLE.' '.IP_SUBDIRECTORIES;
if(!directory_is_writable(dirname(__FILE__).'/../audio')) {
    $table[] = '<span class="error">'.IP_ERROR."</span>";
    $error['writable_audio'] = 1;
}else
$table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = '<b>/file/</b> '.IP_WRITABLE.' '.IP_SUBDIRECTORIES;
if(!directory_is_writable(dirname(__FILE__).'/../file')) {
    $table[] = '<span class="error">'.IP_ERROR."</span>";
    $error['writable_file'] = 1;
}else
$table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = '<b>/image/</b> '.IP_WRITABLE.' '.IP_SUBDIRECTORIES;
if(!directory_is_writable(dirname(__FILE__).'/../image')) {
    $table[] = '<span class="error">'.IP_ERROR."</span>";
    $error['writable_image'] = 1;
}else
$table[] = '<span class="correct">'.IP_OK.'</span>';

$table[] = '<b>/video/</b> '.IP_WRITABLE.' '.IP_SUBDIRECTORIES;
if(!directory_is_writable(dirname(__FILE__).'/../video')) {
    $table[] = '<span class="error">'.IP_ERROR."</span>";
    $error['writable_video'] = 1;
}else
$table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = '<b>/ip_config.php</b> '.IP_WRITABLE;
if(!is_writable(dirname(__FILE__).'/../ip_config.php')) {
    $table[] = '<span class="error">'.IP_ERROR."</span>";
    $error['writable_config'] = 1;
}else
$table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = '<b>/robots.txt</b> '.IP_WRITABLE;
if(!is_writable(dirname(__FILE__).'/../robots.txt')) {
    $table[] = '<span class="error">'.IP_ERROR."</span>";
    $error['writable_robots'] = 1;
}else
$table[] = '<span class="correct">'.IP_OK.'</span>';




$answer .= gen_table($table);

$answer .= '<br><br>';
if(sizeof($error) > 0) {
    $_SESSION['step'] = 1;
    $answer .= '<a class="button_act" href="?step=1">'.IP_CHECK_AGAIN.'</a>';
}else {
    complete_step(1);
    $answer .= '<a class="button_act" href="?step=2">'.IP_NEXT.'</a><a class="button" href="?step=1">'.IP_CHECK_AGAIN.'</a>';
}
$answer .= "<br>";

output($answer);


function get_url() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }

    return $pageURL;
}