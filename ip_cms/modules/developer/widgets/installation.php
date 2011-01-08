<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\developer\widgets;  
 
if (!defined('BACKEND')) exit;  
require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'standard/languages/db.php');

class ModulesInstallation{
  public static function install($groupName, $moduleName){
    if(file_exists(MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/config.ini')){
      $configFile = file(MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/config.ini');
      $config = array();
      foreach($configFile as $key => $configRow){
        $configName = substr($configRow, 0, strpos($configRow, ':'));
        $value = substr($configRow, strpos($configRow, ':') + 1);
        $value = str_replace("\n", "", str_replace("\r", "", $value));
        $config[$configName] = $value;
      }      
    }else
      $config = array();

    $config['module_group_key'] = $groupName;

    if(!isset($config['module_group_name']))
      $config['module_group_name'] = $groupName;
    if(!isset($config['module_group_admin']))
      $config['module_group_admin'] = 0;
      
    $config['module_key'] = $moduleName;
    if(!isset($config['module_name']))
      $config['module_name'] = $moduleName;
    if(!isset($config['module_admin']))
      $config['module_admin'] = 0;
    if(!isset($config['module_managed']))
      $config['module_managed'] = 1;
    if(!isset($config['version']))
      $config['version'] = 1.00;

    $group = Db::getModuleGroup($groupName); 
    if($group === false){
      Db::insertModuleGroup($config['module_group_name'], $config['module_group_key']);
      $group = Db::getModuleGroup($groupName); 
    }
    if($group !== false){       
      
      Db::insertModule($config['module_name'], $config['module_key'], $group['id'], $config['version']);
      $module = Db::getModule($moduleName);
      
      if(file_exists(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/'.$config['module_group_key'].'/'.$config['module_key'].'/install/script.php')){
        require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widgets/'.$config['module_group_key'].'/'.$config['module_key'].'/install/script.php');
      }

      ModulesInstallation::importConfig($groupName, $moduleName);
    }
      
    
  }
  
  private static function importConfig($groupName, $moduleName){
    global $log;
    $siteLanguages = \Modules\standard\languages\Db::getLanguages();

    foreach($siteLanguages as $key => $language){
      $siteLanguages[$key]['code'] = strtolower($siteLanguages[$key]['code']);
    }
  
  
    $configFiles = ModulesInstallation::getConfigFiles(MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/', 'parameters');
    foreach($configFiles as $key => $file){
      $configFiles[$key] = unserialize(file_get_contents(MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/'.$key));
      $configFiles[$key]->languageCode = strtolower($configFiles[$key]->languageCode);
    }
    
    //install default config
    foreach($configFiles as $key => $file){
      if($key == 'parameters.conf'){
        if(isset($siteLanguages[0])){
          $file->languageCode = $siteLanguages[0]['code'];
          $log->log('developer/widgets', 'parameters installation', MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/parameters.conf installation ('.$siteLanguages[0]['code'].')');
          $file->saveParameters($groupName, $moduleName, false);
        }
      }
    }
    
    //install configuration files that match site languages
    foreach($configFiles as $configKey => $file){
      if($configKey !== 'parameters.conf'){ //parameters.conf is already installed
        foreach($siteLanguages as $languageKey => $language){
          if($language['code'] == $file->languageCode){
            $file->languageCode = $siteLanguages[0]['code'];
            $log->log('adeveloper/widgets', 'parameters installation', MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/'.$configKey.' ('.$language['code'].')');
            $file->saveParameters($groupName, $moduleName, false);
            $siteLanguages[$languageKey] == null; //mark language as installed
          }
        }
      }
    }
    
    //install configuration files that are similar to site languages. Eg. en-gb and en
    foreach($configFiles as $configKey => $file){
      if($configKey !== 'parameters.conf'){ //parameters.conf is already installed
        $tmpLang = $file->languageCode;
        $tmpLang = substr($tmpLang, 0, strpos($tmpLang, '-'));
        foreach($siteLanguages as $languageKey => $language){
          if($language !== null && $language['code'] = $tmpLang){ //null - already installed
            $file->languageCode = $siteLanguages[0]['code'];
            $log->log('bdeveloper/widgets', 'parameters installation', MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/'.$configKey.' ('.$language['code'].')');
            $file->saveParameters($groupName, $moduleName, false);
            $siteLanguages[$languageKey] == null; //mark language as installed
          }
        }
      }
    }
    
    

    
  }
  
  public static function getErrors($groupName, $moduleName){
    global $parametersMod;
    $errors = array();
    
    /*check if already installed*/
    $moduleGroups = Db::modules();
    foreach($moduleGroups as $keyGroup => $group){
      foreach($group as $keyModule => $module){
        if($module['g_name'] == $groupName && $module['m_name'] == $moduleName)
          $errors[] = $parametersMod->getValue('developer','modules','admin_translations_install','error_already_exist');
      }
    }
    /*eof check if already installed*/
    
    /*read configuration*/
    if(file_exists(MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/config.ini')){
      $config = file(MODULE_DIR.'standard/content_management/widgets/'.$groupName.'/'.$moduleName.'/install/config.ini');
      $tmpGroupKey = null;
      $tmpNameKey = null;
      foreach($config as $key => $configRow){
        $configName = substr($configRow, 0, strpos($configRow, ':'));
        $value = substr($configRow, strpos($configRow, ':') + 1);
        $value = str_replace("\n", "", str_replace("\r", "", $value));
        switch($configName){
          case 'module_name':
          break;
          case 'module_key':
            $tmpNameKey = $value;
          break;
          case 'group_name':
          break;
          case 'group_key':
            $tmpGroupKey = $value;
          break;
          case 'required_module':
            $tmpModuleExists = false;
            foreach($moduleGroups as $keyGroup => $group){
              foreach($group as $keyModule => $module){
                if($module['g_name']."/".$module['m_name'] == $value)
                  $tmpModuleExists = true;
              }
            }
            if(!$tmpModuleExists)
              $errors[] = $parametersMod->getValue('developer','modules','admin_translations_install','error_required_module').$value;
          break;
        }
      }
      if($tmpGroupKey != null && $tmpNameKey != null && ($groupName != $tmpGroupKey || $moduleName != $tmpNameKey))
        $errors = array_merge(array($parametersMod->getValue('developer','modules','admin_translations_install','error_move_module').MODULE_DIR.'standard/content_management/widgets/'.$tmpGroupKey."/".$tmpNameKey), $errors);
      
    }
    /*eof read configuration*/
    
    return $errors;
  }
  

  
  private static function getConfigFiles($dir, $configPrefix){
    $answer = array();
    if(file_exists($dir) && is_dir($dir)){
      $handle = opendir($dir);
      if($handle !== false){
       while (false !== ($file = readdir($handle))) {
        if(is_file($dir.$file) && strpos($file, $configPrefix) === 0)
         $answer[$file] = array();
       }
      }
    }
    return $answer;
  }  
}
  
