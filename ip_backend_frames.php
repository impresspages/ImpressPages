<?php
/**
 * ImpressPages CMS main backend file
 * 
 * This file iniciates required variables and outputs backend content.
 * 
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Backend;

/** @private */
define('CMS', true); // make sure other files are accessed through this file.
define('BACKEND', true); // make sure other files are accessed through this file.


error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');


if(is_file(__DIR__.'/ip_config.php')) {
  require (__DIR__.'/ip_config.php');
} else {
  require (__DIR__.'/../ip_config.php');
}

require (BASE_DIR.INCLUDE_DIR.'parameters.php');
require (BASE_DIR.INCLUDE_DIR.'db.php');
require (BASE_DIR.FRONTEND_DIR.'site.php');
require (BASE_DIR.MODULE_DIR.'administrator/log/module.php'); 
require (BASE_DIR.INCLUDE_DIR.'error_handler.php');
require (BASE_DIR.BACKEND_DIR.'cms.php');
require (BASE_DIR.BACKEND_DIR.'db.php');
 
  
$parametersMod = new \ParametersMod();


if(\Db::connect()){

  header('X-UA-Compatible: IE=EmulateIE7');
  
  $log = new \Modules\Administrator\Log\Module();

  $site = new \Frontend\Site(); /*to generate links to site and get other data about frontend*/
  $site->init();
  

  $cms = new Cms();
  


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link rel="SHORTCUT ICON" href="favicon.ico" />
</head>
<frameset rows="64px,*" framespacing="0" border="0">
 <frame name="header" noresize="noresize" frameborder=0 scrolling="no" src="<?php echo $cms->generateActionurl('tep_modules'); ?>">
 <frame id="frameContent" name="content" frameborder=0 src="<?php echo $cms->generateActionurl('first_module'); ?>">
 <noframes>
  <body>Your browser don't support frames!</body>
 </noframes>
</frameset>
</html>
<?php

  
      
  
  \Db::disconnect();
}else   trigger_error('Database access');