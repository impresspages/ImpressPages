<?php

/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\developer\localization;

if (!defined('BACKEND')) exit;

require_once (__DIR__.'/db.php');
require_once (__DIR__.'/parameter_db.php');


class Manager{



  public static function saveParameters($file, $ignoreLanguage = false){
    require_once(MODULE_DIR.'standard/languages/db.php');

    //require_once(MODULE_DIR."standard/seo/db.php");
    global $site;
    require($file);
    $answer = '';

    //get languageId
    $langauges = \Db_100::getLanguages();
    $languageId = $languages[0];


    if(isset($parameterGroupTitle)){
      foreach($parameterGroupTitle as $groupName => $group){
        foreach($group as $moduleName => $module){
          foreach($module as $parameterGroupName => $value){
            $tmpModule = Db::getModule(null, $groupName, $moduleName);
            if($tmpModule){
              $tmpParameterGroup = Db::getParameterGroup($tmpModule['id'], $parameterGroupName);
              if($tmpParameterGroup) {
                Db::setParameterGroupTitle($tmpParameterGroup['id'], $value);
              }else{
                if(isset($parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName]))
                  $admin = $parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName];
                else
                  $admin = 1;
                $tmpParameterGroup = Db::createParameterGroup($tmpModule['id'], $parameterGroupName, $value, $admin);
              }
            }
          }
        }
      }
    }


    if(isset($parameterValue)){
      foreach($parameterValue as $groupName => $moduleGroup){
        foreach($moduleGroup as $moduleName => $module){
          $tmpModule = Db::getModule(null, $groupName, $moduleName);
          if($tmpModule){
            foreach($module as $parameterGroupName => $parameterGroup){
              $tmpParameterGroup = Db::getParameterGroup($tmpModule['id'], $parameterGroupName);
              if(!$tmpParameterGroup) {
                if(isset($parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName]))
                  $admin = $parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName];
                else
                  $admin = 1;
                $tmpParameterGroup['id'] = Db::createParameterGroup($tmpModule['id'], $parameterGroupName, $parameterGroupName, $admin);
              }

              foreach($parameterGroup as $parameterName => $value){
                if(!$this->exist($groupName, $moduleName, $parameterGroupName, $parameterName)){
                  $parameter = array();
                  $parameter['name'] = $parameterName;
                  if(isset($parameterAdmin[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                    $parameter['admin'] = $parameterAdmin[$groupName][$moduleName][$parameterGroupName][$parameterName];
                  else
                    $parameter['admin'] = 1;

                  if(isset($parameterTitle[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                    $parameter['translation'] = $parameterTitle[$groupName][$moduleName][$parameterGroupName][$parameterName];
                  else
                    $parameter['translation'] = $parameterName;

                  if(isset($parameterType[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                    $parameter['type'] = $parameterType[$groupName][$moduleName][$parameterGroupName][$parameterName];
                  else
                    $parameter['type'] = 'string';

                  $parameter['value'] = str_replace("\r\n", "\n", $value); //user can edit parameters file and change line endings. So, we convert them back
                  $parameter['value'] = str_replace("\r", "\n", $parameter['value']);
                  Db::insertParameter($tmpParameterGroup['id'], $parameter);
                }
              }
            }

          }
        }
      }
    }


    if(isset($parameterTitle)){
      foreach($parameterTitle as $moduleGroupName => $moduleGroup){
        foreach($moduleGroup as $moduleName => $module){
          foreach($module as $parameterGroupName => $parameterGroup){
             foreach($parameterGroup as $parameterName => $title){
               Db::setParameterTitle($moduleGroupName, $moduleName, $parameterGroupName, $parameterName, $title);
             }
          }
        }
      }
    }


    if(isset($moduleGroupTitle)){
      foreach($moduleGroupTitle as $groupName => $value){
        Db::setModuleGroupTitle($groupName, $value);
      }
    }



    if(isset($moduleTitle)){
      foreach($moduleTitle as $groupName => $group){
        foreach($group as $moduleName => $value){
          Db::setModuleTitle($groupName, $moduleName, $value);
        }
      }
    }



    return $answer;
  }





  function exist($modGroup, $module, $parGroup, $parameter) {
    $tmpModule = \Db::getModule(null, $modGroup, $module);
    if($tmpModule) {
      $parameter = \Db::getParameter($tmpModule['id'], 'module_id', $parGroup, $parameter);
      if($parameter) {
        return true;
      } else {
        return false;
      }

    } else {
      return false;
    }

  }


}


