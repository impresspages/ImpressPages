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
    'DB_PREF' => 'ip_',

    // GLOBAL
    'BASE_DIR' => realpath(TEST_CODEBASE_DIR) . '/',
    'CORE_DIR' => '',
    'BASE_URL' => 'http://localhost/',
    'FILE_DIR' => 'phpunit/tmp/file/',
    'TMP_FILE_DIR' => 'phpunit/tmp/file/tmp/',
    'FILE_REPOSITORY_DIR' => 'phpunit/tmp/file/repository/',

    'DEVELOPMENT_ENVIRONMENT' => 1,
    'ERRORS_SHOW' => 1,
    'ERRORS_SEND' => '',
    // END GLOBAL

    // BACKEND
    'INCLUDE_DIR' => 'ip_cms/includes/',
    'LIBRARY_DIR' => 'ip_libs/',
    'MODULE_DIR' => 'ip_cms/modules/',
    'THEME_DIR' => 'ip_themes/',
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

    'FRONTEND' => 1,
    
);