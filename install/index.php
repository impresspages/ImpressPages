<?php

/**
 * @package ImpressPages
 */

if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages CMS you need PHP >= 5.3.*';
    exit;
}

$config = require dirname(__DIR__) . '/Ip/Module/Install/ip_config-template.php';
$config['BASE_DIR'] = dirname(__DIR__) . '/';

$tmp = explode('/install/', $_SERVER['REQUEST_URI'], 2);
$config['BASE_URL'] = 'http://' . $_SERVER['HTTP_HOST'] . $tmp[0] . '/';

require_once dirname(__DIR__) . '/Ip/Config.php';
\Ip\Config::init($config);

$core = \Ip\Config::getCore('CORE_DIR');

require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Internal/Autoloader.php';

$installation = new \Ip\Module\Install\Application();
$installation->run();
