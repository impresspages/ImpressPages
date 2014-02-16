<?php

/**
 * @package ImpressPages
 */

 return array(
    'SESSION_NAME' => 'ses881187672', // prevents session conflict when two sites runs on the same server
    'DEVELOPMENT_ENVIRONMENT' => 1, // displays error and debug information. Change to 0 before deployment to production server
    'ERRORS_SHOW' => 1, // 0 if you don't wish to display errors on the page
    'TIMEZONE' => 'Europe/Helsinki', // PHP 5 requires timezone to be set.
    'db' => array (
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => 'maskas',
        'tablePrefix' => 'ip_',
        'database' => '4.x',
        'charset' => 'utf8',
      ), // Database configuration
);