<?php
/**
 * ImpressPages main frontend file
 * @package ImpressPages
 */


if ((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages you need PHP >= 5.5';
    exit;
}

// when running using the PHP built-in webserver we have to ensure that we serve images through PHP too
if (php_sapi_name() == 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    if (is_file(__DIR__ . '/' . $url['path'])) {
        return false;
    }
}

require_once __DIR__.'/../vendor/autoload.php';

$application = new \Ip\Application();
$application->run();