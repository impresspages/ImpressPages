<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\developer\modules; 
 
if (!defined('BACKEND')) exit;  

class Db{
 
  public static function deletePermissions($moduleId){
    $sql = "delete from `".DB_PREF."user_to_mod` where `module_id` = '".mysql_real_escape_string($moduleId)."'";
    $rs = mysql_query($sql);
    if(!$rs)
      trigger_error($sql);
  }
  
  public static function addPermissions($moduleId, $userId){
    $sql = "insert into `".DB_PREF."user_to_mod` set `module_id` = '".mysql_real_escape_string($moduleId)."', `user_id` = '".mysql_real_escape_string($userId)."'";
    $rs = mysql_query($sql);
    if(!$rs)
      trigger_error($sql);
  }
  
  /**
   * Sets max row_number for specified module  
   * @param int $moduleId 
   * @return void   
   **/     
  public static function newModuleRowNumber($moduleId){
    $sql_current = "select * from `".DB_PREF."module` where `id` = '".$moduleId."' ";
    $rs_current = mysql_query($sql_current);
    if($rs_current){
      if($current = mysql_fetch_assoc($rs_current)){
        $sql_max = "select max(`row_number`) as 'max_row_number' from `".DB_PREF."module` where `group_id` = '".$current['group_id']."'";
        $rs_max = mysql_query($sql_max);
        if($rs_max){
          $max_row_number = 0;
          if($max = mysql_fetch_assoc($rs_max)){
            $max_row_number = $max['max_row_number'];
          }
          $sql_update = "update `".DB_PREF."module` set `row_number` = '".($max_row_number+1)."' where `id` = ".$moduleId." ";
          $rs_update = mysql_query($sql_update);
          if(!$rs_update)
            trigger_error($sql_update." ".mysql_error());
        }else trigger_error($sql_current." ".mysql_error());
        
      }else trigger_error($sql_current);
    }else trigger_error($sql_current." ".mysql_error());
  }

  /**
   * Sets max row_number for specified module group 
   * @param int $module_group_id 
   * @return void   
   **/
   
  public static function getModuleGroup($groupName){
    $sql = "select * from `".DB_PREF."module_group` where `name` = '".mysql_real_escape_string($groupName)."' ";
    $rs = mysql_query($sql);
    $answer = array();
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      }      
    }else{
      trigger_error($sql." ".mysql_error());
    }
    return false;
  }   
        

        
  public static function updateModuleVersion($moduleGroupKey, $moduleKey, $moduleVersion){
    $sql = "update `".DB_PREF."module` m, `".DB_PREF."module_group` mg 
    set m.`version` = ".((double)$moduleVersion)." 
    where m.`group_id` = mg.`id` and m.`name` = '".mysql_real_escape_string($moduleKey)."' and mg.`name` = '".mysql_real_escape_string($moduleGroupKey)."'
    ";
    $rs = mysql_query($sql);
    if(!$rs){
      trigger_error($sql." ".mysql_error());
    }
  }  
   
  public static function newModuleGroupRowNumber($moduleGroupId){
        $sql_max = "select max(`row_number`) as 'max_row_number' from `".DB_PREF."module_group` where 1";
        $rs_max = mysql_query($sql_max);
        if($rs_max){ 
          $max_row_number = 0;
          if($max = mysql_fetch_assoc($rs_max)){
            $max_row_number = $max['max_row_number'];
          }
          $sql_update = "update `".DB_PREF."module_group` set `row_number` = '".($max_row_number+1)."' where `id` = ".$moduleGroupId." ";
          $rs_update = mysql_query($sql_update);
          if(!$rs_update)
            trigger_error($sql_update." ".mysql_error());
        }else trigger_error($sql_current." ".mysql_error());
  }
  

  public static function getGroups(){
    $sql = "select * from `".DB_PREF."module_group` where 1";
    $rs = mysql_query($sql);
    $answer = array();
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[$lock['name']] = $lock;
      }
      return $answer;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }


  
  public static function getModules($groupId){
    $sql = "select name, version from `".DB_PREF."module` where `group_id` = ".(int)$groupId."";
    $rs = mysql_query($sql);
    $answer = array();
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[$lock['name']] = $lock;
      }      
      return $answer;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }
  
  
  public static function insertModuleGroup($moduleGroupName, $moduleGroupKey, $moduleGroupAdmin){
    $maxRowNumber = 0;

    $sqlMax = "select max(row_number) as maxRowNumber from `".DB_PREF."module_group` where 1 ";
    $rsMax = mysql_query($sqlMax);
    if($rsMax){
      if($lock = mysql_fetch_assoc($rsMax)) {
        $maxRowNumber = $lock['maxRowNumber'] + 1;
      }
    } else {
      trigger_error($sql." ".mysql_error());  
    }
    
    
    $sql = "insert into `".DB_PREF."module_group` set `row_number` = ".(int)$maxRowNumber.", `translation`='".mysql_real_escape_string($moduleGroupName)."',`name`='".mysql_real_escape_string($moduleGroupKey)."',`admin`='".(int)$moduleGroupAdmin."'  ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    }else{
      trigger_error($sql." ".mysql_error());
    }

    return false;
  }

  public static function insertModule($moduleName, $moduleKey, $moduleAdmin, $moduleManaged, $groupId, $version){
    $sql = "insert into `".DB_PREF."module` set `translation`='".mysql_real_escape_string($moduleName)."',`name`='".mysql_real_escape_string($moduleKey)."',`admin`='".(int)$moduleAdmin."',`managed`='".(int)$moduleManaged."', `group_id` = ".(int)$groupId.", `version` = '".mysql_real_escape_string((double)$version)."', `core` = 0  ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    }else
      trigger_error($sql." ".mysql_error());
      
    return false;
  }
}
   
   
