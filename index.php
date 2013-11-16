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

if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages CMS you need PHP >= 5.3.*';
    exit;
}

$config = require (__DIR__.'/ip_config.php');

require_once $config['BASE_DIR'] . $config['CORE_DIR'] . 'Ip/Config.php';
\Ip\Config::init($config);

require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/autoloader.php';

//TODOX remove
ini_set('display_errors', 1);

try {
    $application = new \Ip\Core\Application();
    $application->init();
    $application->run();
} catch (\Exception $e) {
    if (isset($log)) {
        $log->log('System', 'Exception caught', $e->getMessage().' in '.$e->getFile().':'.$e->getLine());
    }
    throw $e;
}
