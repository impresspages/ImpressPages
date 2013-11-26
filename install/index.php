<?php

/**
 * @package ImpressPages
 */

//TODOX review if we want to leave this
error_reporting(E_ALL);
ini_set("display_errors", 1);


if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages CMS you need PHP >= 5.3.*';
    exit;
}

require_once(__DIR__ . '/../Ip/Application.php');

    $application = new \Ip\Application(__DIR__ . '/ip_config.php');
    $application->init();
    $application->prepareEnvironment();
    $options = array(
        'skipModuleInit' => 1,
        'translationsLanguageCode' => 'en'
    );
    $request = new \Plugin\Setup\Request();
    $request->setGet($_GET);
    $request->setPost($_POST);
    $request->setServer($_SERVER);
    $request->setRequest($_REQUEST);
    $response = $application->handleRequest($request, $options);
    $response->send();


//$config = require dirname(__DIR__) . '/Ip/Module/Install/ip_config-template.php';
//$config['BASE_DIR'] = dirname(__DIR__) . '/';
//
//$tmp = explode('/install/', $_SERVER['REQUEST_URI'], 2);
//$config['BASE_URL'] = 'http://' . $_SERVER['HTTP_HOST'] . $tmp[0] . '/';
//
//require_once dirname(__DIR__) . '/Ip/Config.php';
////TODOX create an object
//$config = new \Ip\Config($config);
//\Ip\ServiceLocator::setConfig($config);
//echo 'test';exit;
//$core = ipConfig()->getCore('CORE_DIR');
//
//require_once ipConfig()->getCore('CORE_DIR') . 'Ip/Internal/Autoloader.php';
//
//$installation = new \Ip\Module\Install\Application();
//$installation->run();
