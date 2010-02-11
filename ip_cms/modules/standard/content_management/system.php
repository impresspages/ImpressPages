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
	

	
}