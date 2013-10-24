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
    'CORE_DIR' => realpath(TEST_CODEBASE_DIR) . '/',
    'BASE_URL' => '',
    'FILE_DIR' => 'file/',
    'TMP_FILE_DIR' => 'file/tmp/',
    'FILE_REPOSITORY_DIR' => 'file/repository/',

    'DEVELOPMENT_ENVIRONMENT' => 1,
    'ERRORS_SHOW' => 1,
    'ERRORS_SEND' => '',
    // END GLOBAL

    // BACKEND
    'INCLUDE_DIR' => 'ip_cms/includes/',
    'BACKEND_DIR' => 'ip_cms/backend/',
    'LIBRARY_DIR' => 'ip_libs/',
    'MODULE_DIR' => 'ip_cms/modules/',
    'CONFIG_DIR' => 'ip_configs/',
    'PLUGIN_DIR' => 'ip_plugins/',
    'THEME_DIR' => 'ip_themes/',

    'BACKEND_MAIN_FILE' => 'admin.php',
    'BACKEND_WORKER_FILE' => 'ip_backend_worker.php',
    // END BACKEND

    // FRONTEND
    'CHARSET' => 'UTF-8',

    'THEME' => 'Blank',
    'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5',

    'timezone' => 'Africa/Bujumbura',
    // END FRONTEND

    'SECURE_DIR' => 'file/secure/',
    'TMP_SECURE_DIR' => 'file/secure/tmp/',
    'MANUAL_DIR' => 'file/manual/',
);