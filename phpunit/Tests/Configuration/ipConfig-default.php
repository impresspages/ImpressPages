<?php

return array(
    'SESSION_NAME' => 'TestSession',

    'db' => array(
        'hostname' => 'localhost',
        'username' => 'test',
        'password' => 'test',
        'database' => 'test',
        'tablePrefix' => 'ip_',
        'charset' => 'utf8',
    ),

    // GLOBAL
    'BASE_DIR' => TEST_CODEBASE_DIR, //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    'CORE_DIR' => '',
    'BASE_URL' => 'http://localhost/', //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    'FILE_DIR' => 'phpunit/tmp/file/', //uploded files directory
    'TMP_FILE_DIR' => 'phpunit/tmp/file/tmp/', //temporary files directory
    'FILE_REPOSITORY_DIR' => 'phpunit/tmp/file/repository/', //files repository.
    'SECURE_DIR' => 'phpunit/tmp/file/secure/', //directory not accessible from the Internet
    'TMP_SECURE_DIR' => 'phpunit/tmp/file/secure/tmp/', //directory for temporary files. Not accessible from the Internet.
    'MANUAL_DIR' => 'phpunit/tmp/file/manual/', //Used for TinyMCE file browser and others tools where user manually controls all files.
    'pluginDir' => './Plugin',

    'DEVELOPMENT_ENVIRONMENT' => 1, //displays error and debug information. Change to 0 before deployment to production server
    'ERRORS_SHOW' => 1,  //0 if you don't wish to display errors on the page
    'ERRORS_SEND' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.
    // END GLOBAL

    // BACKEND
    'INCLUDE_DIR' => 'ip_cms/includes/', //system directory
    'LIBRARY_DIR' => 'ip_libs/', //general classes and third party libraries
    'MODULE_DIR' => 'ip_cms/modules/', //system modules directory
    'THEME_DIR' => 'ip_themes/', //themes directory
    // END BACKEND

    // FRONTEND
    'CHARSET' => 'UTF-8', //system characterset
    'THEME' => 'Blank', //theme from themes directory
    'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5', //look ip_cms/includes/Ip/View.php for available options.

    'timezone' => 'Europe/Vilnius',
    // END FRONTEND
    'FRONTEND' => 1,


    'host' => 'localhost',
    'baseDir' => '/var/www/localhost',

    'fileDir' => './file',            // relative dir

    'protocol' => 'http',
    'siteUrlPath' => '/',
    'charset' => 'UTF-8',
);