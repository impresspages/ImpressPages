<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
 
namespace Modules\developer\localization; 
 
if (!defined('CMS')) exit; 
class Db{
  
  
  public static function getModuleGroup($name){
    $sql = "select * from `".DB_PREF."module_group` where `name` = '".mysql_real_escape_string($name)."'";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }      
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }
  
  public static function getModule($groupName, $moduleName){
    $sql = "select m.* from `".DB_PREF."module` m, `".DB_PREF."module_group` g 
    where m.group_id = g.id and m.name = '".mysql_real_escape_string($moduleName)."' and g.name = '".mysql_real_escape_string($groupName)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }      
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }  
  
  public static function getParameterGroup($moduleId, $groupName){
    $sql = "select * from `".DB_PREF."parameter_group` where `module_id` = '".(int)$moduleId."' and `name` = '".mysql_real_escape_string($groupName)."'";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }      
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }  
  
  
  public static function getParameter($moduleGroupName, $moduleName, $parameterGroupName, $parameterName){
    $sql = "select * from `".DB_PREF."module_group` mg, `".DB_PREF."module` m, `".DB_PREF."parameter_group` pg, `".DB_PREF."parameter` p 
    where p.group_id = pg.id and pg.module_id = m.id and m.group_id = mg.id
    and mg.name = '".mysql_real_escape_string($moduleGroupName)."' and m.name = '".mysql_real_escape_string($moduleName)."' and pg.name = '".mysql_real_escape_string($parameterGroupName)."' and p.name = '".mysql_real_escape_string($parameterName)."'";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;        
      }else{
        return false;
      }
      
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }

  }
  

  
  
  public static function setModuleGroupTitle($moduleGroupName,$value){
    $sql = "update `".DB_PREF."module_group` 
    set `translation` = '".mysql_real_escape_string($value)."' 
    where  name = '".mysql_real_escape_string($moduleGroupName)."' ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }  
  
  public static function createParameterGroup($moduleId, $name, $translation, $admin){
    $sql = "insert into `".DB_PREF."parameter_group` set `name`='".mysql_real_escape_string($name)."', `translation`='".mysql_real_escape_string($translation)."', `admin`='".(int)$admin."', `module_id`=".(int)$moduleId." ";
    $rs = mysql_query($sql);
    if(!$rs){
      trigger_error($sql." ".mysql_error());
      return false;
    }else
      return mysql_insert_id();
  }  
  
  public static function setModuleTitle($moduleGroupName, $moduleName, $value){
    $sql = "update `".DB_PREF."module_group` mg, `".DB_PREF."module` m
    set m.translation = '".mysql_real_escape_string($value)."' 
    where m.group_id = mg.id and mg.name = '".mysql_real_escape_string($moduleGroupName)."' and m.name = '".mysql_real_escape_string($moduleName)."'";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }

  public static function setParameterGroupTitle($parameterGroupId, $title){
    $sql = "update `".DB_PREF."parameter_group` 
    set `translation` = '".mysql_real_escape_string($title)."' 
    where  `id` = ".(int)$parameterGroupId." ";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }

  
  
  public static function setParameterTitle($moduleGroupName, $moduleName, $parameterGroupName, $parameterName, $value){
    $sql = "update `".DB_PREF."module_group` mg, `".DB_PREF."module` m, `".DB_PREF."parameter_group` pg, `".DB_PREF."parameter` p
    set p.translation = '".mysql_real_escape_string($value)."' 
    where p.group_id = pg.id and pg.module_id = m.id and m.group_id = mg.id
    and mg.name = '".mysql_real_escape_string($moduleGroupName)."' and m.name = '".mysql_real_escape_string($moduleName)."' and pg.name = '".mysql_real_escape_string($parameterGroupName)."' and p.name = '".mysql_real_escape_string($parameterName)."'";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }

  public static function insertLanguage($languageCode, $languageShort, $languageLong, $languageUrl, $visible, $rowNumber){
    $sql = "insert into `".DB_PREF."language` 
    set row_number = ".(int)$rowNumber.", d_short = '".mysql_real_escape_string($languageShort)."',  d_long = '".mysql_real_escape_string($languageLong)."',  url = '".mysql_real_escape_string($languageUrl)."',  code = '".mysql_real_escape_string($languageCode)."', visible = ".(int)$visible."";
    $rs = mysql_query($sql);
    if($rs){
      return mysql_insert_id();
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
    
  }
  
  
  
  public static function getLanguage($id){
    $answer = array();
    $sql = "select * from `".DB_PREF."language` where id = ".(int)$id."";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      }else{
        return false;
      }
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
    return $answer;
  }
  
    public static function insertParameter($groupId, $parameter){
      $sql = "insert into `".DB_PREF."parameter` 
      set name = '".mysql_real_escape_string($parameter['name'])."',
      admin = '".mysql_real_escape_string($parameter['admin'])."',
      group_id = ".(int)$groupId.",
      translation = '".mysql_real_escape_string($parameter['translation'])."',
      type = '".mysql_real_escape_string($parameter['type'])."'";
    
      $rs = mysql_query($sql);
      if($rs){
        $last_insert_id = mysql_insert_id();
        switch($parameter['type']){
          case "string_wysiwyg":
            $sql = "insert into `".DB_PREF."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
            $rs = mysql_query($sql);
            if(!$rs)
              trigger_error("Can't insert parameter ".$sql." ".mysql_error());
          break;    
          case "string":
            $sql = "insert into `".DB_PREF."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
            $rs = mysql_query($sql);
            if(!$rs)
              trigger_error("Can't insert parameter ".$sql." ".mysql_error());
          break;    
          case "integer":
            $sql = "insert into `".DB_PREF."par_integer` set `value` = ".mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
            $rs = mysql_query($sql);
            if(!$rs)
              trigger_error("Can't insert parameter ".$sql." ".mysql_error());
          break;    
          case "bool":
            $sql = "insert into `".DB_PREF."par_bool` set `value` = ".mysql_real_escape_string($parameter['value']).", `parameter_id` = ".$last_insert_id."";
            $rs = mysql_query($sql);
            if(!$rs)
              trigger_error("Can't insert parameter ".$sql." ".mysql_error());
          break;    
          case "textarea":
            $sql = "insert into `".DB_PREF."par_string` set `value` = '".mysql_real_escape_string($parameter['value'])."', `parameter_id` = ".$last_insert_id."";
            $rs = mysql_query($sql);
            if(!$rs)
              trigger_error("Can't insert parameter ".$sql." ".mysql_error());
          break;    
    
          case "lang":
            $languages = \Frontend\Db::getLanguages();
            foreach($languages as $key => $language){
              $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
              $rs3 = mysql_query($sql3);
              if(!$rs3)
                trigger_error("Can't update parameter ".$sql3." ".mysql_error());            
            }
          break;          
          case "lang_textarea":
            $languages = \Frontend\Db::getLanguages();
            foreach($languages as $key => $language){
              $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
              $rs3 = mysql_query($sql3);
              if(!$rs3)
                trigger_error("Can't update parameter ".$sql3." ".mysql_error());            
            }
          break;          
          case "lang_wysiwyg":
            $languages = \Frontend\Db::getLanguages();
            foreach($languages as $key => $language){
              $sql3 = "insert into `".DB_PREF."par_lang` set `translation` = '".mysql_real_escape_string($parameter['value'])."', `language_id` = '".$language['id']."', `parameter_id` = ".$last_insert_id." ";
              $rs3 = mysql_query($sql3);
              if(!$rs3)
                trigger_error("Can't update parameter ".$sql3." ".mysql_error());            
            }
          break;          
        }      
      }else{
        trigger_error($sql." ".mysql_error());
      }
    }  
  
 
}
   
   
