<?php

/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */



if (!defined('CMS')) exit;

class ParametersRefractor {
   
  

  
  public function deleteParameter($moduleGroup, $module, $parameterGroup, $parameterName){
    $moduleId = $this->getModuleId($moduleGroup, $module);
    $parameterGroup = $this->getParametersGroup($moduleId, $parameterGroup);
    $parameter = $this->getParameter($parameterGroup['id'], $parameterName);
    
    if($parameter){
      $sql = false;
      switch($parameter['type']){
        case 'string_wysiwyg':
        case 'string':
        case 'textarea':
          $sql = "delete from `".DB_PREF."par_string` where parameter_id = ".(int)$parameter['id']."";
        break;
        case 'integer':
          $sql = "delete from `".DB_PREF."par_integer` where parameter_id = ".(int)$parameter['id']."";
        break;
        case 'bool':
          $sql = "delete from `".DB_PREF."par_bool` where parameter_id = ".(int)$parameter['id']."";
        break;
        case 'lang':
        case 'lang_textarea':
        case 'lang_wysiwyg':
          $sql = "delete from `".DB_PREF."par_lang` where parameter_id = ".(int)$parameter['id']."";
        break;
      }
      
      if($sql){
        $rs = mysql_query($sql);
        if(!$rs)
          trigger_error($sql.' '.mysql_error());
        $sql = "delete from `".DB_PREF."parameter` where id = ".(int)$parameter['id']."";
        $rs = mysql_query($sql);
        if(!$rs)
          trigger_error($sql.' '.mysql_error());

      }
    }
  }
  
  private function updateModule($moduleId, $newTranslation, $newName){
    $sql =  "update `".DB_PREF."module` set translation = '".mysql_real_escape_string($newTranslation)."', name = '".mysql_real_escape_string($newName)."' where id = ".(int)$moduleId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    } 
  }
  
  public function addPermissions($moduleId, $userId){
    $sql = "insert into `".DB_PREF."user_to_mod`
    set
    module_id = '".(int)$moduleId."',
    user_id = '".(int)$userId."'
    
    ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }    
  }
  
  public function getUsers(){
    $answer = array();
    $sql = "select * from `".DB_PREF."user` where 1";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
      return $answer;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }

  }
  
  public function addModule($groupId, $moduleTranslation, $moduleName, $admin, $managed, $core, $version, $rowNumber = 0){
    $sql = "insert into `".DB_PREF."module`
    set
    group_id = '".(int)$groupId."',
    name = '".mysql_real_escape_string($moduleName)."',
    translation = '".mysql_real_escape_string($moduleTranslation)."',
    admin = '".(int)$admin."',
    managed = '".(int)$managed."',
    core = '".(int)$core."',
    row_number = '".(int)$rowNumber."',
    version = '".mysql_real_escape_string($version)."'
    
    ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
 
  
  private function getParameter($groupId, $name){
    $answer = array();
    $sql = "select * from `".DB_PREF."parameter` where `group_id` = ".(int)$groupId." and `name` = '".mysql_real_escape_string($name)."'";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else
        return false;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  
  
  private function renameTable($oldName, $newName){
    $sql = "RENAME TABLE `".DB_PREF.$oldName."`  TO `".DB_PREF.$newName."`" ;
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      //trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  

  
  public function destroyModule($groupName, $moduleName){
    $id = $this->getModuleId($groupName, $moduleName);
    $this->deleteModule($id);
    $this->deletePermissions($id);
  }
  
  
  public function deletePermissions($moduleId){
    $sql = "delete from `".DB_PREF."user_to_mod` where `module_id` = ".(int)$moduleId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
    
  
  public function getModuleGroup($name){
    $sql = "select * from `".DB_PREF."module_group` where name = '".mysql_real_escape_string($name)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }  
  }
 
  public function getModules($groupId){
    $answer = array();
    $sql = "select * from `".DB_PREF."module` where `group_id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
      return $answer;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }    
  }
  
  
  public function dropTable($name) {
    $sql = "DROP TABLE `".DB_PREF.$name."`";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      //trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  

  
  public function getModuleGroups($moduleId){
    $answer = array();
    $sql = "select * from `".DB_PREF."parameter_group` where `module_id` = ".(int)$moduleId."";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
      return $answer;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  
  
  public function deleteParametersGroup($id){
    $sql = "delete from `".DB_PREF."parameter_group` where `id` = ".(int)$id." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  public function newGroup($name, $translation, $module_id, $admin){
    $sql = "insert into `".DB_PREF."parameter_group`
    set `name`= '".mysql_real_escape_string()."',
    `translation`= '".mysql_real_escape_string()."',
    `module_id`= ".(int)$module_id.",
    `admin`= ".(int)$admin."";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  public function changeGroupModuleId($groupId, $newModuleId){
    $sql = "update `".DB_PREF."parameter_group` set `module_id` = ".(int)$newModuleId." where `id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  public function changeGroupTranslation($groupId, $translation){
    $sql = "update `".DB_PREF."parameter_group` set `translation` = '".mysql_real_escape_string($translation)."' where `id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }

  public function changeGroupAdmin($groupId, $admin){
    $sql = "update `".DB_PREF."parameter_group` set `admin` = '".(int)$admin."' where `id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  

  public function addParameterGroup($moduleId, $name, $translation, $admin){
    $sql = "insert into `".DB_PREF."parameter_group` 
    set 
    `name` = '".mysql_real_escape_string($name)."',
    `translation` = '".mysql_real_escape_string($translation)."',
    `module_id` = '".(int)$moduleId."',
    `admin` = '".(int)$admin."'
    ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  
  public function changeGroupName($groupId, $name){
    $sql = "update `".DB_PREF."parameter_group` set `name` = '".mysql_real_escape_string($name)."' where `id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  public function changeParametersGroup($oldGroupId, $newGroupId){
    $sql = "update `".DB_PREF."parameter` set `group_id` = ".(int)$newGroupId." where `group_id` = ".(int)$oldGroupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }

  

  
  public function getModuleId($group_name, $module_name){
    $answer = array();
    $sql = "select m.id from `".DB_PREF."module` m, `".DB_PREF."module_group` g 
    where m.`group_id` = g.`id` and g.`name` = '".mysql_real_escape_string($group_name)."' and m.`name` = '".mysql_real_escape_string($module_name)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock['id'];
      } else {
        return false;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
    
  }
  
  public function deleteModule($id){
    $sql = "delete from  `".DB_PREF."module` where id = '".(int)$id."' ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      //trigger_error($sql." ".mysql_error());  
      return false;    
    }    
  }
  
  public function renameModule($id, $newName, $newTranslation){
    $sql = "update `".DB_PREF."module`
    set `name` = '".mysql_real_escape_string($newName)."', `translation` = '".mysql_real_escape_string($newTranslation)."' 
    where `id` = ".(int)$id." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  
  



  public function getParametersGroup($moduleId, $name){
    $sql = "select * from `".DB_PREF."parameter_group` where `module_id` = '".mysql_real_escape_string($moduleId)."' and `name` = '".mysql_real_escape_string($name)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }
  
  

  
  private function addEmptyTranslations($name, $translation){
    $sql = "update `".DB_PREF."parameter` 
    set `translation` = '".mysql_real_escape_string($translation)."' 
    where `name` = '".mysql_real_escape_string($name)."' and `translation` = '' ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    } else {
      trigger_error($sql." ".mysql_error());  
      return false;    
    }
  }  
  
}


