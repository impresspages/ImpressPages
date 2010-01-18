<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\catalog\items;

if (!defined('CMS')) exit;

class Db{

  public static function getItemById($languageId, $id){
    $sql = "select * from `".DB_PREF."m_catalog_item` i, `".DB_PREF."m_catalog_item_translation` t 
    where t.`record_id` = i.`id` and t.`language_id` = ".(int)$languageId."
    and i.`id` = '".mysql_real_escape_string($id)."' ";
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
  
  public static function getItemByUrl($languageId, $url, $parentId = null){
    $sql = "select * from `".DB_PREF."m_catalog_item` i, `".DB_PREF."m_catalog_item_translation` t 
    where t.`record_id` = i.`id` and t.`language_id` = ".(int)$languageId."
    and t.`meta_url` = '".mysql_real_escape_string($url)."' ";
    
    if($parentId !== null){
      if(is_array($parentId))
        $parent = ' in ('.implode(',', $parentId).')';
      else
        $parent = ' = '.(int)$parentId.' ';
      
      $sql .= ' and category_id '.$parent;
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
  
  public static function getPhotos($elementId){
    $sql = "select * from ".DB_PREF."m_catalog_item_photo where `record_id` = '".(int)$elementId."' ";
    $rs = mysql_query($sql);
    if($rs){
      $answer = array();
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
      return $answer;
    } else {
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }

  /**
   * 
   * @param int $languageId
   * @param int or array $parentId //int - one parent id, array - array of id's 
   * @param bool $reverseOrder
   * @param bool $onlyVisible
   * @param int $startFrom
   * @param int $limit
   * @return array/false
   */
  public static function getItems($languageId, $parentId = null, $reverseOrder = false, $onlyVisible = false, $onlyNonZero = false, $startFrom = 0, $limit = null){
    if ($parentId === null || is_array($parentId) && sizeof($parentId) == 0) {
      $sql = "select * from `".DB_PREF."m_catalog_item` i, `".DB_PREF."m_catalog_item_translation` t
       where t.`record_id` = i.`id` and t.`language_id` = ".(int)$languageId." and i.`category_id` is null ";
    } else {
      if(is_array($parentId))
        $parent = ' in ('.implode(', ', $parentId).')';
      else {
        if($parentId == null){
          $parent = ' is null ';
        } else {
          $parent = ' = '.(int)$parentId.' ';          
        }
      }
        
      $sql = "select * from `".DB_PREF."m_catalog_item` i, `".DB_PREF."m_catalog_item_translation` t
       where t.`record_id` = i.`id` and t.`language_id` = ".(int)$languageId." and i.`category_id` ".$parent." ";
    }
    if($onlyVisible){
      $sql .= ' and visible ';
    }
    
    if($onlyNonZero){
      $sql .= ' and quantity > 0 ';
    }
    
    
    if (!$reverseOrder){
      $sql .= ' order by i.`priority` desc, t.`title` asc ';
    } else {
      $sql .= ' order by i.`priority` asc, t.`title` desc ';
    }
    
    if($limit !== null){
      $sql .= ' limit '.(int)$startFrom.', '.(int)$limit.'';
    }    
    
    
    $rs = mysql_query($sql);
    $answer = array();
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
      return $answer;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }  
  


}