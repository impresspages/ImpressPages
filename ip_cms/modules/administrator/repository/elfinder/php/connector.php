<?php

if (!defined('CMS')) {
    define('CMS', true); // make sure other files are accessed through this file.
}

error_reporting(1); // Set E_ALL for debuging

if(is_file(__DIR__.'/../../../../../../ip_config.php')) {
    require (__DIR__.'/../../../../../../ip_config.php');
} else {
    require (__DIR__.'/../../../../../../../ip_config.php');
}

if (DEVELOPMENT_ENVIRONMENT){
    error_reporting(E_ALL|E_STRICT);
    ini_set('display_errors', '1');
}


include_once __DIR__.DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once __DIR__.DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once __DIR__.DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once __DIR__.DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
include_once __DIR__.DIRECTORY_SEPARATOR.'elFinderVolumeIp.class.php';

// Required for MySQL storage connector
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';


/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

$opts = array(
	// 'debug' => true,
	'roots' => array(
		array(
			'driver'        => 'Ip',   // driver for accessing file system (REQUIRED)
			'path'          => BASE_DIR.FILE_REPOSITORY_DIR,         // path to files (REQUIRED)
			'URL'           => BASE_URL.FILE_REPOSITORY_DIR, // URL to files (REQUIRED)
			'accessControl' => 'access',             // disable and hide dot starting files (OPTIONAL)
            'rootAlias'     => FILE_REPOSITORY_DIR,
            'fileURL'       => false
		)
	)
);

// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

