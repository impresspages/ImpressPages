<?php

return array(
    'host' => 'localhost',
    'baseDir' => '/var/www/localhost',

    'pluginDir' => './Plugin',        // relative dir
    'fileDir' => './file',            // relative dir
    'temporaryFileDir' => './file/tmp', // temporary files directory
    'themeDir' => './ip_themes', // themes directory

    'legacyPluginDir' => './ip_plugins', // relative dir
    'legacyIncludeDir' => './ip_includes',
    'legacyIncludeDir' => './ip_cms/includes', // system directory
    'legacyBackendDir' => './ip_cms/backend', // system directory
    'legacyFrontendDir' => './ip_cms/frontend', // system directory
    'legacyLibraryDir' => './ip_libs', // general classes and third party libraries
    'coreModuleDir' => './ip_cms/modules', // system modules directory
    'configDir' => './ip_configs', // modules configuration directory
    'fileRepositoryDir' => '.file/repository', // files repository
    'secureDir' => './file/secure/', //directory not accessible from the Internet
    'secureTemporaryDir' => 'file/secure/tmp/', //directory for temporary files. Not accessible from the Internet
// define('MANUAL_DIR', 'file/manual/'); //Used for TinyMCE file browser and others tools where user manually controls all files.

'protocol' => 'http',
    'siteUrlPath' => '/',

    'sessionName' => 'testSessionName',

    'db' => array(
        'hostname' => 'localhost',
        'username' => 'test',
        'password' => 'test',
        'database' => 'test',
        'tablePrefix' => 'ip_',
        'charset' => 'utf8',
    ),

    'shouldShowErrors' => 1, // 0 if you don't wish to display errors on the page
    'errorReportingEmail' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.

    'isDevelopmentEnvironment' => 0, // displays error and debug information. Change to 0 before deployment to production server

    'theme' => 'Blank', //theme from themes directory
    'defaultDoctype' => 'DOCTYPE_HTML5', //look ip_cms/includes/Ip/View.php for available options

    'charset' => 'UTF-8',
    'timezone' => 'Europe/Vilnius',

);

if (0) {
//    if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
    // GLOBAL
    // define('SESSION_NAME', 'ses1863122212');  //prevents session conflict when two sites runs on the same server
    // END GLOBAL

    // DB
    //define('DB_SERVER', 'localhost'); // eg, localhost
    //define('DB_USERNAME', 'root');
    //define('DB_PASSWORD', '');
    //define('DB_DATABASE', '');
    //define('DB_PREF', 'ip_');
    // END DB

    // GLOBAL
    // define('BASE_DIR', '/var/www/ip3.x/'); //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
    // define('BASE_URL', 'http://local.ip3.x.org/'); //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
//    define('FILE_DIR', 'file/'); //uploded files directory
//    define('TMP_FILE_DIR', 'file/tmp/'); //temporary files directory
//    define('FILE_REPOSITORY_DIR', 'file/repository/'); //files repository.
//    define('SECURE_DIR', 'file/secure/'); //directory not accessible from the Internet
//    define('TMP_SECURE_DIR', 'file/secure/tmp/'); //directory for temporary files. Not accessible from the Internet.
//    define('MANUAL_DIR', 'file/manual/'); //Used for TinyMCE file browser and others tools where user manually controls all files.
//    define('VIDEO_DIR', 'video/'); //uploaded video directory
//    define('TMP_VIDEO_DIR', 'video/tmp/'); //temporary video directory
//    define('VIDEO_REPOSITORY_DIR', 'video/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.
//    define('AUDIO_DIR', 'audio/'); //uploaded audio directory
//    define('TMP_AUDIO_DIR', 'audio/tmp/'); //temporary audio directory
//    define('AUDIO_REPOSITORY_DIR', 'audio/repository/'); //audio repository. Used for TinyMCE and others where user can browse the files.

//    define('DEVELOPMENT_ENVIRONMENT', 1); //displays error and debug information. Change to 0 before deployment to production server
//    define('ERRORS_SHOW', 1);  //0 if you don't wish to display errors on the page
//    define('ERRORS_SEND', ''); //insert email address or leave blank. If email is set, you will get an email when an error occurs.
    // END GLOBAL

    // BACKEND

//    define('INCLUDE_DIR', 'ip_cms/includes/'); //system directory
//    define('BACKEND_DIR', 'ip_cms/backend/'); //system directory
//    define('FRONTEND_DIR', 'ip_cms/frontend/'); //system directory
//    define('LIBRARY_DIR', 'ip_libs/'); //general classes and third party libraries
//    define('MODULE_DIR', 'ip_cms/modules/'); //system modules directory
//    define('CONFIG_DIR', 'ip_configs/'); //modules configuration directory
//    define('PLUGIN_DIR', 'ip_plugins/'); //plugins directory
//    define('THEME_DIR', 'ip_themes/'); //themes directory

//    define('BACKEND_MAIN_FILE', 'admin.php'); //backend root file
//    define('BACKEND_WORKER_FILE', 'ip_backend_worker.php'); //backend worker root file

    // END BACKEND

    // FRONTEND

//    define('CHARSET', 'UTF-8'); //system characterset
//    define('MYSQL_CHARSET', 'utf8');
//    define('THEME', 'Blank'); //theme from themes directory
//    define('DEFAULT_DOCTYPE', 'DOCTYPE_HTML5'); //look ip_cms/includes/Ip/View.php for available options.
    // END FRONTEND
}