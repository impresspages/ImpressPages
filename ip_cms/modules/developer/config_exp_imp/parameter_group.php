<?php

/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\developer\config_exp_imp;

if (!defined('BACKEND')) exit;  

require_once (__DIR__.'/db.php');


class ParameterGroup{
  public $admin;
  public $name;
  public $translation;
  public $moduleName;
  public $moduleTranslation;
  public $moduleGroupTranslation;
  public $moduleGroupName;
  public $parameters;
  public $groupId;

  function __construct($groupData){
    $module = \Db::getModule($groupData['module_id']);
    $this->admin = $groupData['admin'];
    $this->name = $groupData['name'];
    $this->groupId = $groupData['id'];
    $this->translation = $groupData['translation'];
    $this->moduleName = $module['m_name'];
    $this->moduleTranslation = $module['m_translation'];
    $this->moduleGroupName = $module['g_name'];
    $this->moduleGroupTranslation = $module['g_translation'];
    $this->parameters = array();
  }
  
  
  public function loadString($types){
    $parameters = Db::getParString($this->groupId, $types);
    foreach($parameters as $parameterKey => $parameter)
      if(isset($types[$parameter['type']]))
        $this->parameters[] = $parameter; 
  }
    
  public function loadInteger($types){
    $parameters = Db::getParInteger($this->groupId, $types);
    foreach($parameters as $parameterKey => $parameter)
      if(isset($types[$parameter['type']]))
        $this->parameters[] = $parameter; 
  }
  public function loadBool($types){
    $parameters = Db::getParBool($this->groupId, $types);
    foreach($parameters as $parameterKey => $parameter)
      if(isset($types[$parameter['type']]))
        $this->parameters[] = $parameter; 
  }
  public function loadLang($types, $languageId){
    $parameters = Db::getParLang($this->groupId, $types, $languageId);
    foreach($parameters as $parameterKey => $parameter)
      if(isset($types[$parameter['type']]))
        $this->parameters[] = $parameter; 
  }

  public function saveToDb($languageId){
    global $parametersMod;
    
    $tmpModule = \Db::getModule(null, $this->moduleGroupName, $this->moduleName);
    
    if($tmpModule && sizeof($this->parameters) > 0){
      $tmpParameterGroup = Db::getParameterGroup($tmpModule['id'], $this->name);      
      if($tmpParameterGroup) {
        $parameterGroupId = $tmpParameterGroup['id'];
        Db::renameParameterGroup($tmpParameterGroup['id'], $this->translation);
      }else{
        $parameterGroupId = $tmpParameterGroup = Db::createParameterGroup($tmpModule['id'], $this->name, $this->translation, $this->admin);
      }
      foreach($this->parameters as $parameterKey => $parameter){
        $tmpParameter = \Db::getParameter($tmpModule['id'], 'module_id', $this->name, $parameter['name']);
        if($tmpParameter === false){
          Db::insertParameter($parameterGroupId, $parameter);
        }elseif($tmpParameter['type'] != $parameter['type']){
          Db::deleteParameter($tmpParameter['id'], $tmpParameter['type']);
          Db::insertParameter($parameterGroupId, $parameter);
        }else{
          $this->saveParameter($tmpParameter, $parameter['value'], $languageId);
          Db::renameParameter($tmpParameter['id'], $parameter['translation']); 
        }
          
      }
    }
    
  
  }
  
  private function saveParameter($parameter, $newValue, $languageId){
    switch($parameter['type']){
      case 'string_wysiwyg':
      case 'string':
      case 'textarea':
        \Db::setParString($parameter['id'], $newValue);
      break;
      case 'integer':
        \Db::setParInteger($parameter['id'], $newValue);
      break;
      case 'bool':
        \Db::setParBool($parameter['id'], $newValue);
      break;
      case 'lang':
      case 'lang_textarea':
      case 'lang_wysiwyg':
        \Db::setParLang($parameter['id'],$newValue, $languageId);
      break;
    }
  }
  
  public function preview(){


    $answer = '';
    $answer .= '<h2>'.htmlspecialchars($this->name).'</h2>';
    
    if(sizeof($this->parameters) > 0){
      foreach($this->parameters as $parameterKey => $parameter){
        $answer .= '<div class="parameter"><div class="parameterName">'.htmlspecialchars($parameter['translation']).'</div> <div class="parameterValue">'.htmlspecialchars($parameter['value']).'</div><div class="clear"></div></div>';
      }
    }
    
    return $answer;
  }
  
  

}

