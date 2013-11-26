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
    $options = array(
        'skipErrorHandler' => 1
    );
    $application->prepareEnvironment($options);
    $options = array(
        'skipModuleInit' => 1,
        'translationsLanguageCode' => 'en'
    );
    $request = new \Plugin\Install\Request();
    $request->setGet($_GET);
    $request->setPost($_POST);
    $request->setServer($_SERVER);
    $request->setRequest($_REQUEST);
    $response = $application->handleRequest($request, $options);
    $response->send();

