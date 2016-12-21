<?php
/**
 * ImpressPages main frontend file
 * @package ImpressPages
 */


if ((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages you need PHP >= 5.5';
    exit;
}

require_once __DIR__.'/../vendor/autoload.php';

$application = new \Ip\Application();
$application->run();