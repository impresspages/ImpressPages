<?php
	
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
    
    if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
    // GLOBAL
      define('SESSION_NAME', 'ses1802691667');  //prevents session conflict when two sites runs on the same server
    // END GLOBAL
    
    // DB
      define('DB_SERVER', 'localhost'); // eg, localhost
      define('DB_USERNAME', 'test');
      define('DB_PASSWORD', 'test');
      define('DB_DATABASE', 'test_1342998344');
      define('DB_PREF', 'ipt_');
    // END DB
    
    // GLOBAL
      define('BASE_DIR', '/var/www/ip2.x/phpunit/Tmp/2_0rc2/'); //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
      define('BASE_URL', 'http://localhost/phpunit/Tmp/2_0rc2/'); //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
      define('IMAGE_DIR', 'image/');  //uploaded images directory
      define('TMP_IMAGE_DIR', 'image/tmp/'); //temporary images directory
      define('IMAGE_REPOSITORY_DIR', 'image/repository/'); //images repository. Used for TinyMCE and others where user can browse the images.
      define('FILE_DIR', 'file/'); //uploded files directory
      define('TMP_FILE_DIR', 'file/tmp/'); //temporary files directory
      define('FILE_REPOSITORY_DIR', 'file/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.
      define('VIDEO_DIR', 'video/'); //uploaded video directory
      define('TMP_VIDEO_DIR', 'video/tmp/'); //temporary video directory
      define('VIDEO_REPOSITORY_DIR', 'video/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.
      define('AUDIO_DIR', 'audio/'); //uploaded audio directory
      define('TMP_AUDIO_DIR', 'audio/tmp/'); //temporary audio directory
      define('AUDIO_REPOSITORY_DIR', 'audio/repository/'); //audio repository. Used for TinyMCE and others where user can browse the files.
      
      define('DEVELOPMENT_ENVIRONMENT', 1); //displays error and debug information. Change to 0 before deployment to production server
      define('ERRORS_SHOW', 1);  //0 if you don't wish to display errors on the page
      define('ERRORS_SEND', ''); //insert email address or leave blank. If email is set, you will get an email when an error occurs.
    // END GLOBAL
      
    // BACKEND
      
      define('INCLUDE_DIR', 'ip_cms/includes/'); //system directory
      define('BACKEND_DIR', 'ip_cms/backend/'); //system directory
      define('FRONTEND_DIR', 'ip_cms/frontend/'); //system directory
      define('LIBRARY_DIR', 'ip_libs/'); //general classes and third party libraries
      define('MODULE_DIR', 'ip_cms/modules/'); //system modules directory
      define('CONFIG_DIR', 'ip_configs/'); //modules configuration directory
      define('PLUGIN_DIR', 'ip_plugins/'); //plugins directory
      define('THEME_DIR', 'ip_themes/'); //themes directory
      
      define('BACKEND_MAIN_FILE', 'admin.php'); //backend root file
      define('BACKEND_WORKER_FILE', 'ip_backend_worker.php'); //backend worker root file
    
    // END BACKEND
    
    // FRONTEND
    
      define('CHARSET', 'UTF-8'); //system characterset
      define('MYSQL_CHARSET', 'utf8');
      define('THEME', 'lt_pagan'); //theme from themes directory
      define('DEFAULT_DOCTYPE', 'DOCTYPE_HTML5'); //look ip_cms/includes/Ip/View.php for available options.
    
      mb_internal_encoding(CHARSET);  
      date_default_timezone_set('Europe/London'); //PHP 5 requires timezone to be set.
    
    // END FRONTEND  
