<?php

return array(
    'sessionName' => 'testsession',  //prevents session conflict when two sites runs on the same server

    'db' => array(
        'hostname' => TEST_DB_HOST,
        'username' => TEST_DB_USER,
        'password' => TEST_DB_PASS,
        'database' => TEST_DB_NAME,
        'tablePrefix' => 'ip_',
        'charset' => 'utf8',
    ),

    // GLOBAL
    'baseDir' => realpath(TEST_CODEBASE_DIR),
    'baseUrl' => 'localhost/',

    'developmentEnvironment' => 1,
    'errorsShow' => 1,
    // END GLOBAL

    // FRONTEND
    'charset' => 'UTF-8',

    'theme' => 'Air',
    'defaultDoctype' => 'DOCTYPE_HTML5',

    'timezone' => 'Africa/Bujumbura',
    // END FRONTEND

    'fileOverrides' => array(
        'file/' => TEST_TMP_DIR . 'file/',
        'Plugin/' => realpath(TEST_CODEBASE_DIR) . '/install/Plugin/'
    ),

);
