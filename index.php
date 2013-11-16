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
ini_set('display_errors', 1);

require_once('Ip/Core/Application.php');

try {
    $application = new \Ip\Core\Application(__DIR__ . '/ip_config.php');
    $application->init();
    $application->run();
} catch (\Exception $e) {
    if (isset($log)) {
        $log->log('System', 'Exception caught', $e->getMessage().' in '.$e->getFile().':'.$e->getLine());
    }
    throw $e;
}
