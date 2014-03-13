<?php
/**
 * ImpressPages main frontend file
 * @package ImpressPages
 */


if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages you need PHP >= 5.3.3';
    exit;
}


require_once('Ip/Application.php');

try {
    $application = new \Ip\Application(__DIR__ . '/config.php');
    $application->init();
    $application->run();
} catch (\Exception $e) {
    if (isset($log)) {
        $log->log('System', 'Exception caught', $e->getMessage().' in '.$e->getFile().':'.$e->getLine());
    }
    throw $e;
}
