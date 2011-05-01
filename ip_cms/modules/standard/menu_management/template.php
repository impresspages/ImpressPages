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
'<!DOCTYPE html>
<html>
<head>
  <title>ImpressPages</title>
  <link REL="SHORTCUT ICON" HREF="'.BASE_URL.BACKEND_DIR.'/design/images/favicon.ico" />
  <link href="'.BASE_URL.MODULE_DIR.'standard/menu_management/menu_management.css" type="text/css" rel="stylesheet" media="screen" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/default.js"></script>
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js"></script>
  <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/jstree/jquery.cookie.js"></script>
  <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/jstree/jquery.hotkeys.js"></script>
  <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/jstree/jquery.jstree.js"></script>
  <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/menu_management.js"></script>
</head>   
	 
<body>
'.$content.'
</body>
</html>
';
  }

  public static function content ($data) {
    global $parametersMod;
    $answer = '';
    
    $answer .= 
'
  <script type="text/javascript">
  	var postURL = \''.$data['postURL'].'\';
  	var module_id = \''.$data['moduleId'].'\';
  	var security_token = \''.$data['securityToken'].'\'; 
  	var image_dir= \''.$data['imageDir'].'\'; 
  </script>
	<div>
		<div id="controlls"><span onclick="">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'new_page')).'</span></div>
		<div id="tree"></div>
		<div id="page_properties"></div>
		<div id="tree_popup"></div>
	</div>		
';
    return $answer;
  }
  
  
  public static function generatePageProperties ($tabs) {
    global $parametersMod;
    $answer = '';

    $tabsList = '';
    $contentList = '';
    
    $answer .= 
'
<ul>
	<li>'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'delete')).'</li>
</ul>    
';
    
    foreach ($tabs as $tabKey => $tab) {
      $tabsList .= 
'
<li>
'.htmlspecialchars($tab['title']).'
</li>
';
      
      $contentList .= 
'
<li>
'.$tab['content'].'
</li>
';
    }
    
    $answer .= 
'
<ul class="tabs">
'.$tabsList.'
</ul>    
<ul class="content">
'.$contentList.'    
</ul>    
';
    
    return $answer;
  }
  
  
  public static function generateTabGeneral ($element) {
    global $parametersMod;
    $answer = '';
    
    $answer .= 
'
<form id="formGeneral">
<input type="hidden" name="pageId" value="'.htmlspecialchars($element->getId()).'" />
<input type="hidden" name="zoneName" value="'.htmlspecialchars($element->getZoneName()).'" />
'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'button_title')).'
<input name="buttonTitle" value="'.htmlspecialchars($element->getButtonTitle()).'" /><br />
'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'visible')).'
<input type="checkbox" name="visible" '.($element->getVisible() ? 'checked="yes"' : '' ).' /><br />
'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'created_on')).'
<p class="error" id="createdOnError"></p>
<input name="createdOn" value="'.htmlspecialchars(substr($element->getCreatedOn(), 0, 10)).'" /><br />
'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'last_modified')).'
<p class="error" id="lastModifiedError"></p>
<input name="lastModified" value="'.htmlspecialchars(substr($element->getLastModified(), 0, 10)).'" /><br />
<input type="submit" value="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'" />
</form>
';
    
    return $answer;
  }  
  
  public static function generateTabSEO ($element) {
    global $parametersMod;
    
    $answer = '';
    
    $answer .= 
'
<form id="formSEO">
'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'page_title')).'
<input name="pageTitle" value="'.htmlspecialchars($element->getPageTitle()).'" /><br />
'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'keywords')).'
<textarea name="keywords">'.htmlspecialchars($element->getKeywords()).'</textarea><br />
'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'description')).'
<textarea name="description">'.htmlspecialchars($element->getDescription()).'</textarea><br />
'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'url')).'
<input name="url" value="'.htmlspecialchars($element->getURL()).'" /><br />
<input type="submit" value="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'" />

</form>  
';
    
    return $answer;
  }  

  public static function generateTabAdvanced ($element) {
    global $parametersMod;
    
    $answer = '';
    
    $answer .= 
'
<form id="formAdvanced">

'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'type')).'<br />
<input name="type" value="default" '.($element->getType() == 'default' ? 'checked="checkded"' : '' ).' type="radio" />'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'no_redirect')).'<br />
<input name="type" value="inactive" '.($element->getType() == 'inactive' ? 'checked="checkded"' : '' ).'type="radio" />'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'inactive')).'<br />
<input name="type" value="subpage" '.($element->getType() == 'subpage' ? 'checked="checkded"' : '' ).'type="radio" />'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'redirect_to_subpage')).'<br />
<input name="type" value="redirect" '.($element->getType() == 'redirect' ? 'checked="checkded"' : '' ).'type="radio" />'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'redirect_to_external_page')).'
<img class="linkList" id="internalLinkingIcon" onclick="openInternalLinkingTree();" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/img/list.gif"><br />
<p style="display: none;" id="redirectURLError"></p>
<input autocomplete="off" name="redirectURL" value="'.$element->getRedirectUrl().'">
<input type="submit" value="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'" />

</form>    
';
    
    return $answer;
  }  
  
  
}




