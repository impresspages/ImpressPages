<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;
if (!defined('BACKEND')) exit; 


class Template {
  
  
  public static function addLayout ($content) {
    return 
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
  <title>ImpressPages</title>
  <link REL="SHORTCUT ICON" HREF="'.BASE_URL.BACKEND_DIR.'/design/images/favicon.ico" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/default.js"></script>
  <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/jquery.js"></script>
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/jstree/jquery.jstree.js"></script>
  <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/menu_management.js"></script>
</head>   
	 
<body>
'.$content.'
</body>
</html>
';
  }

  public static function content ($data) {
    $answer = '';
    
    $answer .= 
'
  <script type="text/javascript">
  	var postURL = \''.$data['postURL'].'\';
  	var module_id = \''.$data['moduleId'].'\';
  	var security_token = \''.$data['securityToken'].'\'; 
  </script>
	<div>
		<div id="mod_menu_management_tree"></div>
		<div id="mod_menu_management_page_properties">
		</div>
	</div>		
';
    return $answer;
  }
  
}