<?php
/**
 * @package ImpressPages
 *
 *
 */
define('INSTALL', 'true');

define('TARGET_VERSION', '3.6');

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



if(isset($_GET['step'])){
    switch($_GET['step']){
        case 0:
            $cur_step = 0;
            break;
        case 1:
            $cur_step = 1;
            break;
        case 2:
            $cur_step = 2;
            break;
        case 3:
            $cur_step = 3;
            break;
        case 4:
            $cur_step = 4;
            break;
        case 5:
            $cur_step = 5;
            break;
    }


}
if($cur_step > $_SESSION['step']+1){
    $cur_step = $_SESSION['step']+1;
}

//if(!install_available()){
//    $_SESSION['step'] = 5;
//    $cur_step = 5;
//}



// require('install_'.$cur_step.'.php');

if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages CMS you need PHP >= 5.3.*';
    exit;
}

$config = require __DIR__.'/ip_config-template.php';

require_once $config['BASE_DIR'] . $config['CORE_DIR'] . 'Ip/Config.php';
\Ip\Config::init($config);

require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/autoloader.php';

ini_set('display_errors', 1);

try {
    \Ip\Core\Application::init();
    $application = new \Ip\Core\Application();

    $controller = new \Ip\Module\Install\SiteController();

    $action = 'step' . $cur_step;
    echo $controller->$action();

} catch (\Exception $e) {
    throw $e;
}