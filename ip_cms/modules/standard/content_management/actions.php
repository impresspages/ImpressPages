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
			\Db::disconnect();
			exit;
		}
		
		
		
	}

		
   
