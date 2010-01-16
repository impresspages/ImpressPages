<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\developer\zones; 
 
if (!defined('BACKEND')) exit;  

require_once(BASE_DIR.LIBRARY_DIR.'php/text/transliteration.php');

class Db{
  
  public static function getLanguages(){
    $answer = array();
    $sql = "select * from `".DB_PREF."language` where 1 order by row_number";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs))
        $answer[] = $lock;
    }else{
      trigger_error($sql." ".mysql_error());
    }
    return $answer;
  }  
  
  public static function getZone($zoneId){
    $sql = "select * from `".DB_PREF."zone` where id = '".$zoneId."'";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs))
        return $lock;
      else
        return false;
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
       
    
  }
  
  public static function deleteParameters($zoneId){
    $sql = "delete from `".DB_PREF."zone_parameter` where `zone_id` = '".$zoneId."'";
    $rs = mysql_query($sql);
    if($rs){
    }else{
      trigger_error($sql." ".mysql_error());
      return false;
    }
  }
  
  public static function createRootzonesElement($zoneId){
    $languages = Db::getLanguages();
    $zone = Db::getZone($zoneId);
    
    foreach($languages as $key => $language){
      $sql = "insert into `".DB_PREF."content_element` set `visible` = 1";
      $rs = mysql_query($sql);
      if($rs){
        $sql2 = "insert into `".DB_PREF."zone_to_content` set 
        `language_id` = '".mysql_real_escape_string($language['id'])."',
        `zone_id` = '".mysql_real_escape_string($zoneId)."',
        `element_id` = '".mysql_insert_id()."'";
        $rs2 = mysql_query($sql2);
        if(!$rs2)
          trigger_error($sql2." ".mysql_error());
          
        $sql2 = "insert into `".DB_PREF."zone_parameter` set 
        `language_id` = '".mysql_real_escape_string($language['id'])."',
        `zone_id` = '".$zoneId."',
        `url` = '".mysql_real_escape_string(Db::newUrl($language['id'], $zone['translation']))."'";
        $rs2 = mysql_query($sql2);
        if(!$rs2)
          trigger_error($sql2." ".mysql_error());          
      }else{
        trigger_error($sql." ".mysql_error());
      }
    }
  }  
  
  public static function newUrl($language, $title){
    $url = mb_strtolower($title);
    $url = \Library\Php\Text\Transliteration::transform($url);
    $url = str_replace(" ", "-", $url);
    $url = str_replace("/", "-", $url);
    $url = str_replace("\\", "-", $url);
    $url = str_replace("\"", "-", $url);
    $url = str_replace("\'", "-", $url);
    $url = str_replace("„", "-", $url);
    $url = str_replace("“", "-", $url);
    $url = str_replace("&", "-", $url);
    $url = str_replace("%", "-", $url);
    $url = str_replace("`", "-", $url);
    $url = str_replace("!", "-", $url);
    $url = str_replace("@", "-", $url);
    $url = str_replace("#", "-", $url);
    $url = str_replace("$", "-", $url);
    $url = str_replace("^", "-", $url);
    $url = str_replace("*", "-", $url);
    $url = str_replace("(", "-", $url);
    $url = str_replace(")", "-", $url);
    $url = str_replace("{", "-", $url);
    $url = str_replace("}", "-", $url);
    $url = str_replace("[", "-", $url);
    $url = str_replace("]", "-", $url);
    $url = str_replace("|", "-", $url);
    $url = str_replace("~", "-", $url);  
  
  
    $sql = "select url from `".DB_PREF."zone_parameter` where `language_id` = '".mysql_real_escape_string($language)."' ";
    $rs = mysql_query($sql);
    if($rs){
      $urls = array();
      while($lock = mysql_fetch_assoc($rs))
        $urls[$lock['url']] = 1;
        
      $i = '';
      if(isset($urls[$url])){
        while(isset($urls[$url.$i])){
          if($i == '')
          $i = 1;
          else
            $i++;
        }
      }
      return $url.$i;
    }else
      trigger_error("Can't get all urls ".$sql." ");  
  }  
 

}
   
   
