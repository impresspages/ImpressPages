<?php

return array(
    'SESSION_NAME' => 'testsession',  //prevents session conflict when two sites runs on the same server

    'db' => array(
        'hostname' => TEST_DB_HOST,
        'username' => TEST_DB_USER,
        'password' => TEST_DB_PASS,
        'database' => TEST_DB_NAME,
        'tablePrefix' => 'ip_',
        'charset' => 'utf8',
    ),

    // GLOBAL
    'BASE_DIR' => realpath(TEST_CODEBASE_DIR) . '/',
    'BASE_URL' => 'localhost/',

    'DEVELOPMENT_ENVIRONMENT' => 1,
    'ERRORS_SHOW' => 1,
    'ERRORS_SEND' => '',
    // END GLOBAL

    // BACKEND
    'THEME_DIR' => 'Theme/',
    // END BACKEND

    // FRONTEND
    'CHARSET' => 'UTF-8',

    'THEME' => 'Blank',
    'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5',

    'timezone' => 'Africa/Bujumbura',
    // END FRONTEND

    'SECURE_DIR' => 'phpunit/tmp/file/secure/',
    'TMP_SECURE_DIR' => 'phpunit/tmp/file/secure/tmp/',
    'MANUAL_DIR' => 'phpunit/tmp/file/manual/',

    'FILE_OVERRIDES' => array(
        'file/' => TEST_TMP_DIR . 'file/',
    ),

);