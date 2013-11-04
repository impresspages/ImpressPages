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

if (!isset($_SESSION['step'])) {
    $_SESSION['step'] = 0;
}

$cur_step = $_SESSION['step'];

if (isset($_GET['step'])) {
    $cur_step = $_GET['step'];
}

//if ($cur_step > $_SESSION['step']+1) {
//    $cur_step = $_SESSION['step']+1;
//}

// TODOX check if install is done
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

    $language = 'en';

    // TODOX more intelligent check
    if (isset($_GET['lang']) && file_exists(\ip\Config::coreModuleFile('Install/languages/' . $_GET['lang'] . '.php'))) {
        $_SESSION['installation_language'] = $_GET['lang'];
        $language = $_GET['lang'];
    } elseif (isset($_SESSION['installation_language'])) {
        $language = $_SESSION['installation_language'];
    }

    \Ip\Translator::init($language);
    \Ip\Translator::addTranslationFilePattern('phparray', \ip\Config::coreModuleFile('Install/languages'), '%s.php', 'ipInstall');
    $application = new \Ip\Core\Application();

    $controller = new \Ip\Module\Install\SiteController();

    $action = \Ip\Request::getRequest('a', 'step' . $cur_step);

    // TODOX check if method exists
    $response = $controller->$action();

    $application->handleResponse($response);

} catch (\Exception $e) {
    throw $e;
}