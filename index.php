<?php
/**
 *
 * ImpressPages CMS main frontend file
 *
 * This file initiates required variables and outputs the content.
 *
 * @package ImpressPages
 *
 *
 */




/** Make sure files are accessed through index. */

if (!defined('CMS')) {
    define('CMS', true); // make sure other files are accessed through this file.
}
if (!defined('FRONTEND') && !defined('BACKEND')) {
    define('FRONTEND', true); // make sure other files are accessed through this file.
}

if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages CMS you need PHP >= 5.3.*';
    exit;
}

if(is_file(__DIR__.'/ip_config.php')) {
    $config = require (__DIR__.'/ip_config.php');
} else {
    $config = require (__DIR__.'/../ip_config.php');
}

require_once $config['BASE_DIR'] . $config['CORE_DIR'] . 'Ip/Config.php';
\Ip\Config::init($config);

require_once BASE_DIR . CORE_DIR . 'Ip/autoloader.php';

ini_set('display_errors', 1);

try {
    \Ip\Core\Application::init();
    $application = new \Ip\Core\Application();
    $application->run();
} catch (\Exception $e) {
    if (isset($log)) {
        $log->log('System', 'Exception caught', $e->getMessage().' in '.$e->getFile().':'.$e->getLine());
    }
    throw $e;
}
