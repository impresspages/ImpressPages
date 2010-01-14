<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Modules\standard\seo; 
 
if (!defined('BACKEND')) exit;
 
require_once(__DIR__.'/db.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod_html_output.php');

class Manager{

  function manage(){
    global $cms;
    global $parametersMod;
    $html = new \Library\Php\StandardModule\std_mod_html_output();
    
    $languages = Db::siteLanguages();
    $menus = Db::getMenu();
    
    
    if(isset($_POST['action']) && $_POST['action'] == 'save'){
      Db::deleteParameter();
      foreach($_POST['seo'] as $key2 => $menu){
        foreach($menu as $key => $language){
          Db::insertParameter($language);        
        }      
      }
    }
    
    
    $html->tabs_open();
    
    $answer = '';
    $answer = '
		<div class="all">
			<div class="search" style="background-color: #ffffff;">
				<link href="'.BASE_URL.MODULE_DIR.'standard/seo/design/style.css" type="text/css" rel="stylesheet" media="screen" />		

		<form method="post" >
      <input type="hidden" name="action" value="save" >
    ';
    
    $parameters = Db::getParameters(); 

  
    
    foreach($menus as $key2 => $menu){
      $answer .= '<div class="search" style="float: left; background-color: #eeeeee;">';
      $answer .= '<a class="menu_name">'.$menu['translation']."</a>";
      foreach($languages as $key => $language)
					$answer .= '
				<input type="hidden" name="seo['.$key2.']['.$key.'][language_id]" value="'.$language['id'].'" >
				<input type="hidden" name="seo['.$key2.']['.$key.'][menu_id]" value="'.$menu['id'].'" >';
			
			$answer .= '<br />';
			
      $answer .= '<span class="label bolder">'.$parametersMod->getValue('standard','seo','admin_translations','title').'</span><br />';
      foreach($languages as $key => $language)
        $answer .= '<span class="label">'.$language['d_short'].'</span><input type="text" name="seo['.$key2.']['.$key.'][title]" value="'.htmlspecialchars($parameters[$menu['id']][$language['id']]['title']).'">';

			$answer .= '<br /><br />';

			$answer .= '<span class="label bolder">'.$parametersMod->getValue('standard','seo','admin_translations','url').'</span><br />';
			
			foreach($languages as $key => $language)
				$answer .= '<span class="label">'.$language['d_short'].'</span><input type="text" name="seo['.$key2.']['.$key.'][url]" value="'.htmlspecialchars($parameters[$menu['id']][$language['id']]['url']).'">';

			$answer .= '<br /><br />';

			
			$answer .= '<span class="label bolder">'.$parametersMod->getValue('standard','seo','admin_translations','keywords').'</span><br />';
			foreach($languages as $key => $language)
				$answer .= '<span class="label">'.$language['d_short'].'</span><textarea rows="8" name="seo['.$key2.']['.$key.'][keywords]" >'.htmlspecialchars($parameters[$menu['id']][$language['id']]['keywords']).'</textarea>';

			$answer .= '<br /><br />';
				
			$answer .= '<span class="label bolder">'.$parametersMod->getValue('standard','seo','admin_translations','description').'</span><br />';	
			foreach($languages as $key => $language)				
				$answer .= '<span class="label">'.$language['d_short'].'</span><textarea rows="10" name="seo['.$key2.']['.$key.'][description]" >'.htmlspecialchars($parameters[$menu['id']][$language['id']]['description']).'</textarea>';
      $answer .= '</div>';
    }    
    
    
    
    $answer .= '
      <div class="clear"></div>
      <br /><br />
      <input class="button" type="submit" value="'.$parametersMod->getValue('standard','seo','admin_translations','save').'">
    </form>
		</div>
		</div>
		'; 
    
    
		
    return $cms->html->headerModule().$answer.$cms->html->footer();
  } 
}
