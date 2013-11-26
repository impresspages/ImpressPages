<?php

return array(
    // GLOBAL
    'SESSION_NAME' => 'install', //prevents session conflict when two sites runs on the same server
    // END GLOBAL

    // DB
    'db' => array(
        'hostname' => 'localhost',
        'username' => 'rootc',
        'password' => 'maskas',
        'database' => '4.x',
        'tablePrefix' => 'ip3_',
        'charset' => 'utf8',
    ),

    // GLOBAL
    'BASE_DIR' => '', //root DIR with trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    'CORE_DIR' => '..',
    'BASE_URL' => getParentUrl(), //root url with trailing slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    'FILE_DIR' => 'file', //uploaded files directory
    'TMP_FILE_DIR' => 'file/tmp', //temporary files directory
    'FILE_REPOSITORY_DIR' => 'file/repository', //files repository.
    'SECURE_DIR' => 'file/secure', //directory not accessible from the Internet
    'TMP_SECURE_DIR' => 'file/secure/tmp', //directory for temporary files. Not accessible from the Internet.
    'MANUAL_DIR' => 'file/manual', //Used for TinyMCE file browser and others tools where user manually controls all files.

    'DEVELOPMENT_ENVIRONMENT' => 1, //displays error and debug information. Change to 0 before deployment to production server
    'ERRORS_SHOW' => 1,  //0 if you don't wish to display errors on the page
    'ERRORS_SEND' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.
    // END GLOBAL

    // BACKEND
    'PLUGIN_DIR' => '', //plugins directory
    'THEME_DIR' => 'Theme', //themes directory

    // END BACKEND

    // FRONTEND
    'CHARSET' => 'UTF-8', //system characterset
    'THEME' => 'CentrusCleanus', //theme from themes directory
    'DEFAULT_DOCTYPE' => 'DOCTYPE_HTML5', //look ip_cms/includes/Ip/View.php for available options.

    'timezone' => 'Europe/Vilnius', //TODOX replace with current
    // END FRONTEND



);

function getParentUrl() {
    $pageURL = '';
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }

    $pageURL = substr($pageURL, 0, strrpos($pageURL, '/'));
    return $pageURL;
}
