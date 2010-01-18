<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\shop\currencies;

if (!defined('CMS')) exit;

class Db{

  public static function getCurrencyById($id){
    $sql = "select * from `".DB_PREF."m_shop_currency` 
    where `id` = ".(int)$id." limit 1";
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
  
  public static function getCurrencyByCode($code){
    $sql = "select * from `".DB_PREF."m_shop_currency` 
    where `code` = ".mysql_real_escape_string($code)." limit 1";
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
  
  
  public static function getDefaultCurrency(){
    $sql = "select * from `".DB_PREF."m_shop_currency` 
    where `default` limit 1";
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
  
  public static function getCurrencyByLanguage($languageId){
    $sql = "select c.* from `".DB_PREF."m_shop_currency` c, `".DB_PREF."m_shop_currency_to_language` cl 
    where cl.`currency_id` = c.`id` and cl.`language_id` = ".(int)$languageId." limit 1";
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
  
  public static function getCurrencies(){
    $sql = "select * from `".DB_PREF."m_shop_currency` 
    where 1 order by row_number";
    $rs = mysql_query($sql);
    if($rs){
      $answer = array();
      while($lock = mysql_fetch_assoc($rs)){
        $answer[] = $lock;
      }
      return $answer;
    }else{
      trigger_error($sql." ".mysql_error());
    }
    return false;
    
  }
  
  public static function setDefault($id){
    $sql = "update `".DB_PREF."m_shop_currency` 
    set `default` = (".(int)$id." = `id`) where 1";
    $rs = mysql_query($sql);
    if($rs){
      return true;
    }else{
      trigger_error($sql." ".mysql_error());
    }
    return false;
    
  }
 

}