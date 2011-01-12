<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Modules\developer\widgets; 
 
if (!defined('CMS')) exit; 
class Db{
  
  public static function modules(){
    global $cms;
    $groups = array();
    $sql = "select g.name as g_name, g.id, g.translation from `".DB_PREF."content_module_group` g
		where 1 order by row_number";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $groups[$lock['translation']] = array();    
        
        $sql2 = "select m.name as m_name, m.id, m.translation from 
        `".DB_PREF."content_module` m where m.group_id = '".$lock['id']."' 
         order by row_number";
        $rs2 = mysql_query($sql2);
        if($rs2){
          while($lock2 = mysql_fetch_assoc($rs2)){
            $lock2['g_name'] =  $lock['g_name'];
            $groups[$lock['translation']][] = $lock2;
          }
          if(sizeof($groups[$lock['translation']]) == 0)
            unset($groups[$lock['translation']]);
        }else trigger_error($sql." ".mysql_error());
      }
    }else trigger_error($sql." ".mysql_error());
    return $groups;
  }  
  
  public static function getModuleGroups(){
    $sql = "select id, name from `".DB_PREF."content_module_group` where 1";
    $rs = mysql_query($sql);
    $answer = array();
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[$lock['name']] = Db::getModules($lock['id']);
      }      
      return $answer;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }
  
  public static function getModules($groupId){
    $sql = "select name from `".DB_PREF."content_module` where `group_id` = ".(int)$groupId."";
    $rs = mysql_query($sql);
    $answer = array();
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[$lock['name']] = array();
      }      
      return $answer;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }
  
  public static function getModuleGroup($groupName){
    $sql = "select * from `".DB_PREF."content_module_group` where `name` = '".mysql_real_escape_string($groupName)."' ";
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

  public static function getModule($moduleId){
    $sql = "select m.*, m.name as module_name, g.name as group_name from `".DB_PREF."content_module` m, `".DB_PREF."content_module_group` g where m.group_id = g.id and m.id = '".(int)$moduleId."' ";
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
  
  public static function insertModuleGroup($moduleGroupName, $moduleGroupKey){
    $sql = "insert into `".DB_PREF."content_module_group` set `row_number` = ".(Db::maxGroupRowNumber() + 1).", `translation`='".mysql_real_escape_string($moduleGroupName)."',`name`='".mysql_real_escape_string($moduleGroupKey)."'  ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    }else
      trigger_error($sql." ".mysql_error());
      
    return false;
  }  
  
  public static function insertModule($moduleName, $moduleKey, $groupId, $version){
    $sql = "insert into `".DB_PREF."content_module` set `row_number` = ".(Db::maxModuleRowNumber($groupId) + 1).",`translation`='".mysql_real_escape_string($moduleName)."',`name`='".mysql_real_escape_string($moduleKey)."', `group_id` = ".(int)$groupId.", `version` = '".mysql_real_escape_string($version)."'   ";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    }else
      trigger_error($sql." ".mysql_error());
      
    return false;
  }  
  
  public static function maxModuleRowNumber($groupId){
    $sql = "select max(row_number) as max_row_number from `".DB_PREF."content_module` where `group_id` = ".(int)$groupId." ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs))
        return $lock['max_row_number'];
      else
        return 1;
    }else
      trigger_error($sql." ".mysql_error());
    return false;
  
  }
  
  public static function maxGroupRowNumber(){
    $sql = "select max(row_number) as max_row_number from `".DB_PREF."content_module_group` where 1 ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs))
        return $lock['max_row_number'];
      else
        return 1;
    }else
      trigger_error($sql." ".mysql_error());
    return false;
  
  }
}
   
   
