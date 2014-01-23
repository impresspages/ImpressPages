<?php
/**
 * @package ImpressPages
 *
 *
 */
define('INSTALL', 'true');

define('TARGET_VERSION', '3.9');

//$_SESSION['step'] - stores the value of completed steps


date_default_timezone_set('Europe/Vilnius'); //PHP 5 requires timezone to be set.


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


function install_available(){
    if(filesize("../ip_config.php") !== false && filesize("../ip_config.php") < 100){
        return true;
    }else{
        return false;
    }
}





function output($html, $requiredJs = array()){

    $jsHtml = '';
    foreach($requiredJs as $jsFile) {
        $jsHtml .= '<script type="text/javascript" src="'.$jsFile.'"></script>'."\n";
    }

    echo
	'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <title>'.IP_INSTALLATION.'</title>
    <link rel="stylesheet" href="design/style.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>
<body>

    <div class="container">
        <img id="logo" src="design/cms_logo.png" alt="ImpressPages CMS">
        <div class="clear"></div>
        <div id="wrapper">
            <p id="installationNotice">'.IP_INSTALLATION.' <span>'.IP_VERSION.'</span></p>
            <div class="clear"></div>
            <img class="border" src="design/cms_main_top.gif" alt="Design">
            <div id="main">
                <div id="menu">
'.gen_menu().'
                </div>
                <div id="content">
'.$html.'
                </div>
                <div id="loading">
                <img src="design/loading.gif" ?>
                </div>
                <div class="clear"></div>
            </div>
            <img class="border" src="design/cms_main_bottom.gif" alt="Design">
            <div class="clear"></div>
        </div>
        <div class="footer">Copyright 2009-'.date("Y").' by <a href="http://www.impresspages.org">ImpressPages UAB</a></div>
    </div>

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/init.js"></script>
    '.$jsHtml.'

</body>';
}


function gen_menu(){
    global $cur_step;
    $steps = array();
    $steps[] = IP_STEP_LANGUAGE;
    $steps[] = IP_STEP_CHECK;
    $steps[] = IP_STEP_LICENSE;
    $steps[] = IP_STEP_DB;
    $steps[] = IP_STEP_CONFIGURATION;
    $steps[] = IP_STEP_COMPLETED;

    $answer = '
    <ul>
';

    foreach($steps as $key => $step){
        $class = "";
        if($_SESSION['step'] >= $key)
        $class="completed";
        else
        $class="incompleted";
        if($key == $cur_step)
        $class="current";
        if($key < $cur_step)
        $answer .= '<li onclick="document.location=\'index.php?step='.($key).'\'" class="'.$class.'"><a href="index.php?step='.($key).'">'.$step.'</a></li>';
        else
        $answer .= '<li class="'.$class.'"><a>'.$step.'</a></li>';

    }

    $answer .= '
    </ul>
';

    return $answer;
}

function complete_step($step){
    //if($_SESSION['step'] < $step)
    $_SESSION['step'] = $step;
}


function gen_table($table){
    $answer = '';

    $answer .= '<table>';
    $i = 0;
    while(sizeof($table) > ($i + 1)){
        $answer .= '<tr><td class="label">'.$table[$i].'</td><td class="value">'.$table[$i+1].'</td></tr>';
        $i += 2;
    }

    $answer .= '</table>';
    return $answer;
}

session_start();



if(isset($_GET['lang']) && file_exists('translations/'.$_GET['lang'].'.php')){
    $_SESSION['installation_language'] = $_GET['lang'];
    require_once('translations/'.$_GET['lang'].'.php');
} else {
    if(isset($_SESSION['installation_language'])){
        require_once('translations/'.$_SESSION['installation_language'].'.php');
    } else {
        require_once('translations/en.php');
    }
}


if(!isset($_SESSION['step']))
$_SESSION['step'] = 0;

$cur_step = $_SESSION['step'];

if(isset($_GET['step']) && in_array($_GET['step'], range(0,5))){
    $cur_step = $_GET['step'];
}

if($cur_step > $_SESSION['step']+1){
    $cur_step = $_SESSION['step']+1;
}

if(!install_available()){
    $_SESSION['step'] = 5;
    $cur_step = 5;
}



require('install_'.$cur_step.'.php');



?>