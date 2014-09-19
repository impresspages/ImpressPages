<?php
/**
 * ImpressPages main frontend file
 * @package ImpressPages
 */


if ((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages you need PHP >= 5.3.3';
    exit;
}

$configFilename = __DIR__ . '/config.php';
require_once 'Ip/script/run.php';
