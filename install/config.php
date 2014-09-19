<?php

$baseUrl = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

$baseUrl = substr($baseUrl, 0, strrpos($baseUrl, '/')) . '/';

if (getenv('TRAVIS')) {
    $baseUrl =  'localhost/phpunit/tmp/installTest/install/';
}

try {
    $dateTimeObject = new \DateTime();
    $currentTimeZone = $dateTimeObject->getTimezone()->getName();
} catch (Exception $e) {
    $currentTimeZone = 'UTC';
}

return array(
    // GLOBAL
    'sessionName' => 'install', //prevents session conflict when two sites runs on the same server
    // END GLOBAL

    // DB
    'db' => array(
        'hostname' => '',
        'username' => '',
        'password' => '',
        'database' => '',
        'tablePrefix' => '',
        'charset' => '',
    ),

    // GLOBAL
    'baseDir' => dirname(dirname(__FILE__)), //root DIR with trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    'baseUrl' => $baseUrl, //root url with trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.

    'developmentEnvironment' => 1, //displays error and debug information. Change to 0 before deployment to production server
    'showErrors' => 1,  //0 if you don't wish to display errors on the page
    // END GLOBAL

    // FRONTEND
    'theme' => 'CentrusCleanus', //theme from themes directory

    'timezone' => $currentTimeZone,
    // END FRONTEND

    'fileOverrides' => array(
        'Plugin/' => __DIR__ . '/Plugin/',
        'Theme/' => __DIR__ . '/Theme/',
    ),

    'urlOverrides' => array(
        'Plugin/' => "//{$baseUrl}Plugin/",
        'Theme/' => "//{$baseUrl}Theme/",
        'Ip/' => '//' . dirname($baseUrl) . '/Ip/',
    ),

    'services' => array(
        'pageAssets' => 'Plugin\\Install\\PageAssets',
    )
);

