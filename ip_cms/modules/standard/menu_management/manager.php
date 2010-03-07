<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;


if (!defined('BACKEND')) exit;
 
require_once (__DIR__.'/edit_menu_tree.php');


class Manager{
	var $tree;
	var $management;
	var $currentElement;
	var $currentMenu;
	var $currentLanguage;
   
  function __construct(){
    
		$this->tree = new EditMenuTree();
		if ($this->currentLanguage != null){
			$this->tree->setCurrentLanguage($this->currentLanguage);
    }
		if ($this->currentMenu != null)
			$this->tree->setCurrentMenu($this->currentMenu);
		$this->management = null;
		$this->currentElement = null;
  }
   

	function manage(){
    global $cms;
    return $this->makeHtml();
    return $cms->html->headerModule().$this->makeHtml().$cms->html->footer();
   }
   
	function makeHtml(){
	  global $cms;
	  
    $answer = '';	   
	/*	$answer .= '
		<div class="search" style="background-color: #eeeeee;">
		<div id="modMenuManagementLeft">
		'.$this->tree->manageMenu().'
		</div>
		</div>
		';*/
		
    global $std_mod_db;
    global $parametersMod;
    global $cms;

		$answer = '';
		
		$answer .= '
		
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title>ImpressPages</title>
  <link REL="SHORTCUT ICON" HREF="'.BASE_URL.BACKEND_DIR.'/design/images/favicon.ico" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/default.js"></script>
  <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tabs.js"></script>
</head>   
	 
<body> <!-- display loading until page is loaded-->
			
      <!-- display loading util page is loaded-->
      <div id="loading" style="height: 60px; z-index: 1001; width: 100%; position: fixed; left:0px; top: 180px;">
				<table style="margin-left: auto; margin-right: auto;"><tr>
					<td style="font-family: Verdana, Tahoma, Arial; font-size: 14px; color: #505050; padding: 30px 33px; background-color: #d9d9d9; border: 1px solid #bcbdbf;">
						'.htmlspecialchars($parametersMod->getValue('standard', 'configuration', 'system_translations', 'loading')).'
					</td>
				</tr></table>
			</div>
      <script type="text/javascript">
      //<![CDATA[
				LibDefault.addEvent(window, \'load\', init);
	      				
	      function init(){
		      document.getElementById(\'loading\').style.display = \'none\';
	      }
      //]]>
      </script>
      <!-- display loading until page is loaded-->		
		
		<link href="'.BASE_URL.LIBRARY_DIR.'php/standard_module/design/style.css" type="text/css" rel="stylesheet" media="screen" />		
		<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'php/standard_module/design/scripts.js"></script>
		<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tabs.js"></script>
		<script type="text/javascript" src="'.LIBRARY_DIR.'js/windowsize.js" ></script>
		<script type="text/javascript" src="'.LIBRARY_DIR.'js/mouse.js" ></script>
		<script type="text/javascript" src="'.LIBRARY_DIR.'js/positioning.js" ></script>
		<script type="text/javascript" src="'.LIBRARY_DIR.'js/default.js" ></script>
		
		';
		

		$answer .= '
		
		 <div class="all" onmousemove="setPos(event)" onmouseup="mouseButtonPos=\'up\'">';
				$answer .= '<script type="text/javascript">LibDefault.addEvent(window,\'load\',perVisaPloti);</script>';
			
		$answer .= '
        <div id="treeView">
         
      		'.$this->tree->manageMenu().'
      
         </div>
      	<!-- id="treeView" -->	
        <div onmousedown="getPos(event)" id="splitterBar" >
        </div>    
    ';


		  $answer .= '<div id="bodyView">';
			 
		 $answer .= '  <div style="display: none;" id="content">	 
			
			
			<!-- content -->
	     
			
			
			<div id="backtrace_path">
        <a id="backgrace_path_update" style="cursor: default;" class="navigation">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'page_properties')).'</a>
        <a id="backgrace_path_new" style="cursor: default;" class="navigation">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'new_page')).'</a>
      </div>
      <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/communication.js"></script>
      
			<form class="stdMod" id="property_form" onsubmit="ModuleStandardMenuManagement.save(); return false;">
        <div class="search">
          <input name="property_id" value="" type="hidden" />
          <input name="action" value="" type="hidden" />
          
          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'button_title')).'</span><br>
          <p style="display: none;" id="property_button_title_error" class="error"></p>
          <input autocomplete="off" class="stdMod" name="property_button_title" value=""><br><br>
  
          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'page_title')).'</span><br>
          <p style="display: none;" id="property_page_title_error" class="error"></p>
          <input autocomplete="off" class="stdMod" name="property_page_title" value=""><br><br>
  
          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'keywords')).'</span><br>
          <p style="display: none;" id="property_keywords_error" class="error"></p>
          <textarea class="stdMod" name="property_keywords" ></textarea><br><br>

          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'description')).'</span><br>
          <p style="display: none;" id="property_description_error" class="error"></p>
          <textarea class="stdMod" name="property_description" ></textarea><br><br>

          <input  style="cursor: pointer;" class="knob bolder" value="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'" type="submit"><br/><br/>

          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'url')).'</span><br>
          <p style="display: none;" id="property_url_error" class="error"></p>
          <span id="url_prefix" class="label"></span><span style="margin-left: -7px;" id="url_suffix" class="label"></span><br>
          <input onKeyUp="ModuleStandardMenuManagement.setUrlSuffix(this.value);" onChange="ModuleStandardMenuManagement.setUrlSuffix(this.value);" autocomplete="off" class="stdMod" name="property_url" value="admin_translations"><br><br/>
  
          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'type')).'</span><br>
          <input id="property_type_default" name="property_type" value="default" class="stdModBox" type="radio" /><span class="label">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'no_redirect')).'</span><br/>
          <input id="property_type_inactive" name="property_type" value="inactive" class="stdModBox" type="radio" /><span class="label">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'inactive')).'</span><br/>
          <input id="property_type_subpage" name="property_type" value="subpage" class="stdModBox" type="radio" /><span class="label">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'redirect_to_subpage')).'</span><br/>
          <input id="property_type_redirect" name="property_type" value="redirect" class="stdModBox" type="radio" /><span class="label">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'redirect_to_external_page')).'</span><br/>
          <p style="display: none;" id="property_type_error" class="error">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_type_url_empty')).'</p>
          <input autocomplete="off" class="stdMod" name="property_redirect_url" value=""><br/><br/>

          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'visible')).'</span><br><p style="display: none;" id="std_mod_update_f_error_i_n_2" class="error"></p>
          <p style="display: none;" id="property_visible_error" class="error"></p>
          <input checked="checked" class="stdModBox" name="property_visible" type="checkbox"><br><br>

          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'rss')).'</span><br><p style="display: none;" id="std_mod_update_f_error_i_n_2" class="error"></p>
          <p style="display: none;" id="property_rss_error" class="error"></p>
          <input checked="checked" class="stdModBox" name="property_rss" type="checkbox"><br><br>

          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'created_on')).'</span><br>
          <p style="display: none;" id="property_created_on_error" class="error">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format')).date("Y-m-d").'</p>
          <input autocomplete="off" class="stdMod" name="property_created_on" value=""><br><br>

          <span class="label bolder">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'last_modified')).'</span><br>
          <p style="display: none;" id="property_last_modified_error" class="error">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'error_date_format')).date("Y-m-d").'</p>
          <input autocomplete="off" class="stdMod" name="property_last_modified" value=""><br><br>


          <input style="cursor: pointer;" class="knob bolder" value="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'" type="submit">
				</div>
      </form>
          
			<!-- content -->
      
		   			
			
			
			
			
			
			
			
			
		   </div><!-- class="content" -->
		  </div><!-- id="bodyView" -->';
		  
		  $answer .= 
			'<div class="clear">
		  </div>
		 </div><!-- class="all" -->
		 
		   </body>
      </html>   
		 ';

 
     
	return $answer;		
		
		
		return $answer;
	}
  

 

}

