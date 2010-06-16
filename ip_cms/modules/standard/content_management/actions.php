<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management; 

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'administrator/email_queue/module.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/form/standard.php');
require_once (__DIR__.'/widgets/widget.php');

$tmpModules = Db::menuModules();

foreach($tmpModules as $groupKey => $group)
  foreach ($group as $moduleKey => $module){
    require_once (__DIR__.'/widgets/'.$module['group_name'].'/'.$module['module_name'].'/module.php');
  }

	class Actions{
		var $db; 
		function __construct(){		
			$this->db = new Db();
		}
		
		function makeActions(){
			global $site;
			global $parametersMod;
      
      require_once(BASE_DIR.MODULE_DIR.'administrator/email_queue/module.php');

      if(isset($_REQUEST['cm_group']) && isset($_REQUEST['cm_name'])){
        eval (' $new_module = new \\Modules\\standard\\content_management\\Widgets\\'.$_REQUEST['cm_group'].'\\'.$_REQUEST['cm_name'].'\\Module(); ');
        $new_module->makeActions();
      }

			if(isset($_POST['id'])){

        $road = $site->getZone($site->currentZone)->getRoadToElement($_POST['id']);        
        $urlVars = array();        
        foreach($road as $key => $value)
          $urlVars[] = $value->getUrl();

				echo 'window.location.href = \''.$site->generateUrl(null, $site->currentZone, $urlVars).'\';';
				
			}
			
			if(isset($_POST['action']) && $_POST['action'] == 'sitemap_list'){
			  $list = $this->getSitemapInList();
			  echo $list;
			}
			
			\Db::disconnect();
			exit;
		}
		
		
    public function getSitemapInList(){
      global $site;
      $answer = '';
      $answer .= '<ul id="ipSitemap">'."\n";
      
      $answer .= '<li><a href="'.BASE_URL.'">Home</a></li>'."\n";
      
      $languages = \Frontend\Db::getLanguages(true);//get all languages including hidden
      
      foreach($languages as $language){
        $link = $site->generateUrl($language['id']);
        $answer .= '<li><a href="'.$link.'">'.htmlspecialchars($language['d_long']).' ('.htmlspecialchars($language['d_short']).')</a>'."\n";
        
        $zones = $site->getZones();
        if(sizeof($zones) > 0){
          $answer .= '<ul>';
          foreach($zones as $key => $zone){
            $answer .= '<li><a href="'.$site->generateUrl($language['id'], $zone->getName()).'">'.$zone->getTitle().'</a>'."\n";
            $answer .= $this->getPagesList($language, $zone);
            $answer .= '</li>'."\n";
          }
          $answer .= '</ul>';
          
        }
        
        $answer .= '</li>'."\n";
      }
      

      $answer .= '<ul>'."\n";

      $answer = str_replace('?cms_action=manage', '', $answer);
      $answer = str_replace('&cms_action=manage', '', $answer);

      return $answer;
    }
    
    public function getPagesList($language, $zone, $parentElementId = null){
      $answer = '';
      $pages = $zone->getElements($language['id'], $parentElementId, $startFrom = 0, $limit = null, $includeHidden = true, $reverseOrder = false);
      if($pages && sizeof($pages) > 0){
        $answer .= '<ul>'."\n";
        foreach($pages as $key => $page){
          $answer .= '<li><a href="'.$page->getLink().'">'.$page->getButtonTitle().'</a>';
          $answer .= $this->getPagesList($language, $zone, $page->getId());
          $answer .= '</li>';
        }
        $answer .= '</ul>'."\n";
      }
      return $answer;
    }

		
	}
	
		
   
