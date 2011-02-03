<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\system;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


require_once (__DIR__.'/module.php');

class Actions {


  function makeActions() {
    if(isset($_REQUEST['action'])){
      switch($_REQUEST['action']){
        case 'getSystemInfo':
            $module = new Module();
            $systemInfo = $module->getSystemInfo();


            if(isset($_REQUEST['afterLogin'])) { // request after login.
              if($systemInfo == '') {
                $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
                return;
              } else {
                $md5 = \DbSystem::getSystemVariable('last_system_message_shown');
                if($systemInfo && (!$md5 || $md5 != md5($systemInfo)) ) { //we have a new message
                  $newMessage = false;

                  foreach(json_decode($systemInfo) as $infoKey => $infoValue) {
                    if($infoValue->type != 'status') {
                      $newMessage = true;
                    }
                  }

                  $_SESSION['modules']['administrator']['system']['show_system_message'] = $newMessage; //display system alert
                } else { //this message was already seen.
                  $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
                  return;
                }

              }
            } else { //administrator/system tab.
              \DbSystem::setSystemVariable('last_system_message_shown', md5($systemInfo));
              $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
            }


            echo $systemInfo;
        break;

      }

    }

    \Db::disconnect();
    exit;
  }




}



