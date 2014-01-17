<?php

/**
 * @package ImpressPages
 */

 return array(
    'SESSION_NAME' => 'ses7084', // prevents session conflict when two sites runs on the same server
    'DEVELOPMENT_ENVIRONMENT' => 1, // displays error and debug information. Change to 0 before deployment to production server
    'ERRORS_SHOW' => 1, // 0 if you don't wish to display errors on the page
    'CHARSET' => 'UTF-8', // system characterset
    'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5', // look ip_cms/includes/Ip/View.php for available options.
    'TIMEZONE' => 'UTC', // PHP 5 requires timezone to be set.
    'db' => array (
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'tablePrefix' => 'ip_',
        'database' => 'ip4',
        'charset' => 'utf8',
      ), // Database configuration
);