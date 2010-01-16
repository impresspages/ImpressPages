<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\seo;

if (!defined('BACKEND')) exit; 

class Db{
  
  
  public static function getMenu(){
    $answer = array();
    $sql = "select m.id, m.translation from `".DB_PREF."zone` m  where 1 order by row_number";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs))
        $answer[] = $lock;
    }else
      trigger_error($sql." ".mysql_error());
    return $answer;
  }
  
  
  public static function deleteParameter(){
    $languages = Db::siteLanguages();
    $visible_ids = array();
    foreach($languages as $key => $language){
      $visible_ids[] = $language['id'];
    }
    $idsStr = implode(',', $visible_ids);
    
      
    $sql = "delete from `".DB_PREF."zone_parameter` where language_id in (".$idsStr.")";
    $rs = mysql_query($sql);
    if(!$rs)
      trigger_error($sql." ".mysql_error());
  }
  
  public static function insertParameter($parameter){
    $sql = "insert into `".DB_PREF."zone_parameter` set 
    url = '".mysql_real_escape_string(Db::newUrl($parameter['language_id'], $parameter['url']))."',
    description = '".mysql_real_escape_string($parameter['description'])."',
    title = '".mysql_real_escape_string($parameter['title'])."',
    keywords = '".mysql_real_escape_string($parameter['keywords'])."',
    zone_id = '".mysql_real_escape_string($parameter['menu_id'])."',
    language_id = '".mysql_real_escape_string($parameter['language_id'])."'
    
    ";
    
    $rs = mysql_query($sql);
    if(!$rs)
      trigger_error($sql." ".mysql_error());
 
  }
  

  
  public static function newUrl($language, $url){
    $sql = "select url from `".DB_PREF."zone_parameter` where language_id = '".mysql_real_escape_string($language)."' ";
    $rs = mysql_query($sql);
    //require_once(BACKEND_DIR."cms.php");
    if($rs){
      $urls = array();
      while($lock = mysql_fetch_assoc($rs))
        $urls[$lock['url']] = 1;
        
      if(isset($urls[$url]) || \Backend\Cms::usedUrl($url)){
        $i = 1;
        while(isset($urls[$url.$i]) || \Backend\CmS::usedUrl($url.$i)){
          $i++;
        }
        return $url.$i;
      }else
        return $url;
    }else
      trigger_error("Can't get all urls ".$sql." ");  
  }    
  
  
  public static function getParameters(){
    $answer = array();
    $sql = "select * from `".DB_PREF."zone_parameter` where 1 ";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $answer[$lock['zone_id']][$lock['language_id']] = $lock;
      }
    }else
      trigger_error($sql." ".mysql_error());
    return $answer;  
  }
  
  
    public static function siteLanguages(){
      $answer = array();
      $sql = "select id, d_long, d_short from `".DB_PREF."language` where visible order by row_number  ";
      $rs = mysql_query($sql);
      if($rs){
        while($lock = mysql_fetch_assoc($rs))
          $answer[] = $lock;
      }else trigger_error($sql." ".mysql_error());
      return $answer;
    }  
  
}

