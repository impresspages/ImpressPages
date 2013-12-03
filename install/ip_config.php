<?php

return array(
    // GLOBAL
    'SESSION_NAME' => 'install', //prevents session conflict when two sites runs on the same server
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
    'BASE_DIR' => dirname(dirname(__FILE__)), //root DIR with trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    'BASE_URL' => getCurUrl(), //root url with trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.

    'DEVELOPMENT_ENVIRONMENT' => 1, //displays error and debug information. Change to 0 before deployment to production server
    'ERRORS_SHOW' => 1,  //0 if you don't wish to display errors on the page
    'ERRORS_SEND' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.
    // END GLOBAL

    // FRONTEND
    'CHARSET' => 'UTF-8', //system characterset
    'THEME' => 'CentrusCleanus', //theme from themes directory
    'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5', //look ip_cms/includes/Ip/View.php for available options.

    'timezone' => 'Europe/Vilnius', //TODOX replace with current
    // END FRONTEND

    'FILE_OVERRIDES' => array(
        'Plugin/' => __DIR__ . '/Plugin/',
        'Theme/' => __DIR__ . '/Theme/',
    ),

    'URL_OVERRIDES' => array(
        'Plugin/' => 'http://' . getCurUrl() . '/Plugin/',//TODOX find the way to add domain
        'Theme/' => 'http://' . getCurUrl() . '/Theme/',
        'Ip/' => 'http://' . getCurUrl() . '/../Ip/',
    )
);

function getCurUrl() {
    $pageURL = '';
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }

    $pageURL = substr($pageURL, 0, strrpos($pageURL, '/'));
    return $pageURL;
}
