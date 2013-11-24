<?php

/**
 * @package ImpressPages
 *
 *
 */

return array(
    // GLOBAL
    /*OK*/'SESSION_NAME' => 'ses1863122212', //prevents session conflict when two sites runs on the same server
    // END GLOBAL

    // DB
    'db' => array(
        'hostname' => 'localhost',
        'username' => '',
        'password' => '',
        'database' => '',
        'tablePrefix' => 'ip_',
        'charset' => 'utf8',
    ),
    // END DB

    // GLOBAL
    /*OK*/'BASE_DIR' => '', //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    /*OK*/'CORE_DIR' => '',
    /*OK*/'BASE_URL' => 'http://localhost/', //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    /*OK*/'FILE_DIR' => 'file/', //uploded files directory
    /*OK*/'TMP_FILE_DIR' => 'file/tmp/', //temporary files directory
    /*OK*/'FILE_REPOSITORY_DIR' => 'file/repository/', //files repository.
    /*OK*/'SECURE_DIR' => 'file/secure/', //directory not accessible from the Internet
    /*OK*/'TMP_SECURE_DIR' => 'file/secure/tmp/', //directory for temporary files. Not accessible from the Internet.
    /*OK*/'MANUAL_DIR' => 'file/manual/', //Used for TinyMCE file browser and others tools where user manually controls all files.
    /*OK*/'PLUGIN_DIR' => 'Plugin/',

    /*OK*/'DEVELOPMENT_ENVIRONMENT' => 1, //displays error and debug information. Change to 0 before deployment to production server
    /*OK*/'ERRORS_SHOW' => 1,  //0 if you don't wish to display errors on the page
    /*OK*/'ERRORS_SEND' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.
    // END GLOBAL

    // BACKEND
    /*OK*/'THEME_DIR' => 'Themes/', //themes directory
    // END BACKEND

    // FRONTEND
    /*OK*/'CHARSET' => 'UTF-8', //system characterset
    /*OK*/'THEME' => 'Blank', //theme from themes directory
    /*OK*/'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5', //look ip_cms/includes/Ip/View.php for available options.

    'timezone' => 'Europe/Vilnius',
    // END FRONTEND
);