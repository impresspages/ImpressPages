<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\system;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (BASE_DIR.INCLUDE_DIR.'db_system.php');
require_once (BASE_DIR.MODULE_DIR.'developer/modules/db.php');


class Actions {


  function makeActions() {
    if(isset($_REQUEST['action'])){
      switch($_REQUEST['action']){
        case 'getSystemInfo':
          if(function_exists('curl_init')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://service.impresspages.org');
            curl_setopt($ch, CURLOPT_POST, 1);

            $postFields = 'module_name=communication&module_group=service&action=getInfo&version=1';
            $postFields .= '&systemVersion='.\DbSystem::getSystemVariable('version');

            $groups = \Modules\developer\modules\Db::getGroups();
            foreach($groups as $groupKey => $group){
              $modules = \Modules\developer\modules\Db::getModules($group['id']);
              foreach($modules as $moduleKey => $module){
                $postFields .= '&modules[\''.$group['name'].'\'][\''.$module['name'].'\']='.$module['version'];
              }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $answer = curl_exec($ch);
            echo $answer;
          }
        break;

      }

    }

    \Db::disconnect();
    exit;
  }




}



