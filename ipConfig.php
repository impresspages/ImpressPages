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
        'hostname' => DB_SERVER,
        'username' => DB_USERNAME,
        'password' => DB_PASSWORD,
        'database' => DB_DATABASE,
        'tablePrefix' => DB_PREF,
        'charset' => MYSQL_CHARSET,
    ),

    'shouldShowErrors' => 1, // 0 if you don't wish to display errors on the page
    'errorReportingEmail' => '', //insert email address or leave blank. If email is set, you will get an email when an error occurs.

    'isDevelopmentEnvironment' => 0, // displays error and debug information. Change to 0 before deployment to production server

    'theme' => 'Blank', //theme from themes directory
    'defaultDoctype' => 'DOCTYPE_HTML5', //look ip_cms/includes/Ip/View.php for available options

    'charset' => 'UTF-8',
    'timezone' => 'Europe/Vilnius',

);