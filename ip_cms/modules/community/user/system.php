<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\community\user;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

global $site;
$site->requireConfig('community/user/config.php');

class System {



  public function catchEvent($moduleGroup, $moduleName, $event, $parameters) {
    global $session;
    global $parametersMod;

    if (!isset($session) || $session->loggedIn()) {  //in admin.php $session is not defined on time of this event.
      return;
    }

    if (!$parametersMod->getValue('community','user','options','enable_autologin')) {
      return;
    }
    if ($moduleGroup == 'administrator' && $moduleName == 'system' && $event == 'init') {
      if (isset($_COOKIE[Config::$autologinCookieName])) {
        $jsonData = $_COOKIE[Config::$autologinCookieName];
        $data = json_decode($jsonData);
        if ($data && isset($data->id) && isset($data->pass) ) {
          $tmpUser = Db::userById($data->id);
          if ($tmpUser) {
            if (md5($tmpUser['password'].$tmpUser['created_on']) == $data->pass) {
              $session->login($tmpUser['id']);
              setCookie(
                      Config::$autologinCookieName,
                      json_encode(array('id' => $tmpUser['id'], 'pass' => md5($tmpUser['password'].$tmpUser['created_on']))),
                      time() + $parametersMod->getValue('community','user','options','autologin_time') * 60 * 60 * 24,
                      Config::$autologinCookiePath,
                      Config::getCookieDomain()
                      );    
            }
          }
        }
      }
    }

  }

}