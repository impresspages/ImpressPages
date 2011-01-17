<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
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

            $postFields = 'module_name=communication&module_group=service&action=getInfo&version=1&afterLogin=';
            $postFields .= '&systemVersion='.\DbSystem::getSystemVariable('version');

            $groups = \Modules\developer\modules\Db::getGroups();
            foreach($groups as $groupKey => $group){
              $modules = \Modules\developer\modules\Db::getModules($group['id']);
              foreach($modules as $moduleKey => $module){
                $postFields .= '&modules['.$group['name'].']['.$module['name'].']='.$module['version'];
              }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);            
            curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $answer = curl_exec($ch);


            if(isset($_REQUEST['afterLogin'])) { // request after login.
              if($answer == '') {
                $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
                return;
              } else {
                $md5 = \DbSystem::getSystemVariable('lat_system_message_md5');
                if( !$md5 || $md5 != json_encode(md5($answer)) ) { //we have a new message
                  $_SESSION['modules']['administrator']['system']['show_system_message'] = true; //display system alert
                } else { //this message was already seen.
                  $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
                  return;
                }

              }
            } else { //administrator/system tab.
              \DbSystem::setSystemVariable('lat_system_message_md5', json_encode(md5($answer)));
              $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
            }

            echo $answer;
          }
        break;

      }

    }

    \Db::disconnect();
    exit;
  }




}



