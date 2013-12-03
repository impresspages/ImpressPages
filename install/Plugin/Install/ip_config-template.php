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
    /*OK*/'BASE_URL' => 'http://localhost/', //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.

    /*OK*/'DEVELOPMENT_ENVIRONMENT' => 1, //displays error and debug information. Change to 0 before deployment to production server
    /*OK*/'ERRORS_SHOW' => 1,  //0 if you don't wish to display errors on the page
    /*OK*/'ERRORS_SEND' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.
    // END GLOBAL

    // FRONTEND
    /*OK*/'CHARSET' => 'UTF-8', //system characterset
    /*OK*/'THEME' => 'Blank', //theme from themes directory
    /*OK*/'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5', //look ip_cms/includes/Ip/View.php for available options.

    'timezone' => 'Europe/Vilnius',
    // END FRONTEND
);