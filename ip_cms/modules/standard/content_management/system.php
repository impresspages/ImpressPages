<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management;  

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (__DIR__.'/backend_worker.php');
require_once (__DIR__.'/db.php');

class System{

	function clearCache($cachedBaseUrl){
	  
    $tmpModules = Db::menuModules();
    
    require_once (__DIR__.'/widgets/widget.php');
    
    foreach($tmpModules as $groupKey => $group) {
      foreach ($group as $moduleKey => $module) {
      
        require_once (__DIR__.'/widgets/'.$module['group_name'].'/'.$module['module_name'].'/module.php');
        
        eval('$tmpObject = new \\Modules\\standard\\content_management\\Widgets\\'.$module['group_name'].'\\'.$module['module_name'].'\\Module();');
        if (method_exists($tmpObject, 'clearCache')) {
          $tmpObject->clearCache();
        }
      }	  
	  }
    $tmpWorker = new BackendWorker();
    $content_elements = Db::getRealElements();
    foreach($content_elements as $key => $id){
      $_REQUEST['id'] = $id;
      $tmpWorker->make_html();
    }
	}
	
  public function catchEvent($moduleGroup, $moduleName, $event, $parameters){
    
    if($moduleGroup == 'developer' && $moduleName == 'zones' && $event == 'zone_deleted'){
      require_once(__DIR__.'/backend_worker.php');
      
      $backendWorker = new BackendWorker();
      
      $languages = Db::languages();
      foreach($languages as $key => $language){
        $rootElement = Db::rootMenuElement($parameters['zone_id'], $language['id']);
         global $log;
         $log->log('test','root element', serialize($rootElement));
        $elements = Db::menuElementChildren($rootElement);
        foreach($elements as $key => $element){
         $log->log('test','element', serialize($element));
          $backendWorker->remove_element($element['id']);
        }
        
        Db::removeZoneToContent($parameters['zone_id'], $language['id']);
      }

    } 

    if($moduleGroup == 'standard' && $moduleName == 'languages' && $event == 'language_deleted'){
      require_once(__DIR__.'/backend_worker.php');
      
      $backendWorker = new BackendWorker();
      
      $zones = \Frontend\Db::getZones($parameters['language_id']);
      foreach($zones as $key => $zone){
        $rootElement = Db::rootMenuElement($zone['id'], $parameters['language_id']);
         global $log;
         $log->log('test','root element', serialize($rootElement));
        $elements = Db::menuElementChildren($rootElement);
        foreach($elements as $key => $element){
         $log->log('test','element', serialize($element));
          $backendWorker->remove_element($element['id']);
        }
        
        Db::removeZoneToContent($parameters['language_id'], $zone['id']);
      }

    } 
    
  
  }
	
}