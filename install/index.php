<?php

/**
 * @package ImpressPages
 */

if((PHP_MAJOR_VERSION < 5) || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)) {
    echo 'Your PHP version is: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'. To run ImpressPages CMS you need PHP >= 5.3.*';
    exit;
}

require_once(__DIR__ . '/../Ip/Application.php');

    $application = new \Ip\Application(__DIR__ . '/config.php');
    $application->init();
    $options = array(
        'skipErrorHandler' => 1
    );
    $application->prepareEnvironment($options);
    $options = array(
        'skipModuleInit' => 1,
        'translationsLanguageCode' => 'lt'
    );

    $translator = \Ip\ServiceLocator::translator();

    $translator->addTranslationFilePattern(
        'json',
        ipFile('Plugin/Install/translations/'),
        'Install-%s.json',
        'Install'
    );
    $translator->addTranslationFilePattern(
        'json',
        ipFile('file/translations/override/'),
        'Install-%s.json',
        'Install'
    );

    $request = new \Plugin\Install\Request();
    $request->setQuery($_GET);
    $request->setPost($_POST);
    $request->setServer($_SERVER);
    $request->setRequest($_REQUEST);

    $response = $application->handleRequest($request, $options);
    $response->send();

