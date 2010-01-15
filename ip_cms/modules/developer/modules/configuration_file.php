<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
 
namespace Modules\developer\modules; 
 
if (!defined('BACKEND')) exit;  

class ConfigurationFile{
  private $moduleTitle;
  private $moduleKey;
  private $moduleAdmin;
  private $moduleManaged;
  private $moduleVersion;
  private $moduleGroupTitle;
  private $moduleGroupKey;
  private $moduleGroupAdmin;
  private $requiredModules;
  private $error;
  
  public function __construct($file){
    global $parametersMod;
    
    $this->requiredModules = array();
    
    $moduleGroupAdmin = 0;
    $moduleAdmin = 0;

    $config = $this->getInitVariables($file);
        
    foreach($config as $variable){
      $key = $variable['name'];
      $value = $variable['value'];
      switch($key){
        case 'module_title':
          $this->moduleTitle = $value;
        break;
        case 'module_key':
          $this->moduleKey = $value;
        break;
        case 'module_admin':
          $this->moduleAdmin = $value;
        break;
        case 'module_managed':
          $this->moduleManaged = $value;
        break;
        case 'version':
          $this->moduleVersion = $value;
        break;
        case 'module_group_title':
          $this->moduleGroupTitle = $value;
        break;
        case 'module_group_key':
          $this->moduleGroupKey = $value;
        break;
        case 'module_group_admin':
          $this->moduleGroupAdmin = $value;
        break;
        case 'required_module':
          $moduleGroup = substr($value, 0, strpos($value, '/'));
          $value = substr($value, strpos($value, '/')+1);
          $module = substr($value, 0, strpos($value, '/'));
          $version = (double)substr($value, strpos($value, '/')+1);
          $requiredModule = array();
          $requiredModule['module_group_key'] = $moduleGroup;
          $requiredModule['module_key'] = $module;
          $requiredModule['version'] = (double)$version;
          if($moduleGroup == '' || $module == '' || $version == 0){
            $this->setError($parametersMod->getValue('developer', 'modules', 'admin_translations_install', 'error_incorrect_ini_file'));
          }          
          $this->requiredModules[] = $requiredModule;          
        break;
      }
    }

    if($this->moduleKey== null || $this->moduleGroupKey== null || $this->moduleVersion== null || $this->moduleManaged == null){
      $this->setError($parametersMod->getValue('developer', 'modules', 'admin_translations_install', 'error_incorrect_ini_file')); 
    }

    if(!$this->moduleGroupTitle){
      $this->moduleGroupTitle = $this->moduleGroupKey;
    }
    if(!$this->moduleTitle){
      $this->moduleTitle = $this->moduleKey;
    }
    
    
    
    
  }
  

  public function getModuleTitle(){
    return $this->moduleTitle;
  }
  public function getModuleKey(){
    return $this->moduleKey;
  }
  public function getModuleAdmin(){
    return $this->moduleAdmin;
  }
  public function getModuleManaged(){
    return $this->moduleManaged;
  }
  public function getModuleVersion(){
    return $this->moduleVersion;
  }
  public function getModuleGroupTitle(){
    return $this->moduleGroupTitle;
  }
  public function getModuleGroupKey(){
    return $this->moduleGroupKey;
  }
  public function getModuleGroupAdmin(){
    return $this->moduleGroupAdmin;
  }
  public function getRequiredModules(){
    return $this->requiredModules;
  }
  
  public function getError(){
    return $this->error;
  }
  
  private function setError($message){
    $this->error = $message;    
  }
  
  
  private function getInitVariables($file){
    $answer = array();
    
    if(file_exists($file)){
      $config = file($file);
      foreach($config as $key => $configRow){
        $configName = substr($configRow, 0, strpos($configRow, ':'));
        $value = substr($configRow, strpos($configRow, ':') + 1);
        $value = str_replace("\n", "", str_replace("\r", "", $value));
        $answer[] = array('name'=>$configName, 'value' => $value);
      }
    } else {
      global $parametersMod;
      trigger_error($parametersMod->getValue('developer', 'modules', 'admin_translations_install', 'error_ini_file_doesnt_exist').' '.$file);      
    }
    return $answer;
  }
    
}