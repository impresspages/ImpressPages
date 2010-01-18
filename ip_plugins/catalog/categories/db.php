<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\catalog\categories;

if (!defined('CMS')) exit;

class Db{

  public static function getCategoryById($languageId, $id){
    $sql = "select * from `".DB_PREF."m_catalog_category` c, `".DB_PREF."m_catalog_category_translation` t 
    where t.`record_id` = c.`id` and t.`language_id` = ".(int)$languageId."
    and c.`id` = '".mysql_real_escape_string($id)."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }      
    }else{
      trigger_error($sql." ".mysql_error());
    }
    return false;
  }
  
  public static function getCategoryByUrl($languageId, $url, $parentId = null){
    $sql = "select * from `".DB_PREF."m_catalog_category` c, `".DB_PREF."m_catalog_category_translation` t 
    where t.`record_id` = c.`id` and t.`language_id` = ".(int)$languageId."
    and t.`url` = '".mysql_real_escape_string($url)."' ";
    
    if($parentId !== null){
      $sql .= ' and parent_id = '.(int)$parentId.' ';
    }
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;
      } else {
        return false;
      }      
    }else{
      trigger_error($sql." ".mysql_error());
    }
    return false;
  }  
  


  
  public static function getCategories($languageId, $parentId = null, $reverseOrder = false, $onlyVisible = false, $startFrom = 0, $limit = null){
    if ($parentId === null) {
      $sql = "select * from `".DB_PREF."m_catalog_category` c, `".DB_PREF."m_catalog_category_translation` t
       where t.`record_id` = c.`id` and t.`language_id` = ".(int)$languageId." and c.`parent_id` is null ";
    } else {
      $sql = "select * from `".DB_PREF."m_catalog_category` c, `".DB_PREF."m_catalog_category_translation` t
       where t.`record_id` = c.`id` and t.`language_id` = ".(int)$languageId." and c.`parent_id` = ".(int)$parentId." ";
    }
    
    if($onlyVisible){
      $sql .= ' and visible ';
    }
    
    if (!$reverseOrder){
      $sql .= ' order by c.`priority` desc, t.`button_title` asc ';
    } else {
      $sql .= ' order by c.`priority` asc, t.`button_title` desc ';
    }
    if($limit !== null){
      $sql .= ' limit '.(int)$startFrom.', '.(int)$limit.'';
    }

    $rs = mysql_query($sql);
    $answer = array();
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[$lock['id']] = $lock;
      }
      return $answer;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }  
  
  
  
 

}