<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;


if (!defined('BACKEND')) exit;



class Model {

  /**
   * Get all languages
   * @return array
   */
  public static function getLanguages () {
    require_once (BASE_DIR.MODULE_DIR.'standard/languages/db.php'); 
    $languages = \Modules\standard\languages\Db::getLanguages();
    return $languages;
  }

  /**
   * Get zones that are associated with menu management
   */
  public static function getZones () {
    
    
		global $parametersMod;
		
		$managedZones = explode("\n",$parametersMod->getValue('standard', 'menu_management', 'options', 'associated_zones'));
		$sqlZonesArray = "'".implode("','",$managedZones)."'";

		$autoRssZones = explode("\n",$parametersMod->getValue('standard', 'menu_management', 'options', 'auto_rss_zones'));
		$sqlAutoRssArray = "'".implode("','",$autoRssZones)."'";
		
    $dbZones = array();
    $sql = "select z.name, z.translation, z.id, z.name in (".$sqlAutoRssArray.") as `auto_rss`, p.url, p.description, p.keywords, p.title from `".DB_PREF."zone` z, `".DB_PREF."zone_parameter` p where p.zone_id = z.id and z.name in (".$sqlZonesArray.") order by z.row_number  ";
    $rs = mysql_query($sql);
    if($rs){
      while($lock = mysql_fetch_assoc($rs)){
        $dbZones[$lock['name']] = $lock;
      }
    }else trigger_error($sql." ".mysql_error());    
    
    $answer = array();
    foreach($managedZones as $key => &$zone){
      if(isset($dbZones[$zone])){        
        $answer[$zone] = $dbZones[$zone];
      }
    }
    
    
    
    return $answer;
  }

  /**
   * 
   * returns 
   * @param int $contentElementId
   * @param int $languageId
   * @return array root element of content
   */
  public static function rootContentElement($contentElementId, $languageId){
    $sql = "select mte.element_id from `".DB_PREF."zone_to_content` mte, `".DB_PREF."language` l where l.id = '".$languageId."' and  mte.language_id = l.id and zone_id = '".$contentElementId."' ";
    $rs = mysql_query($sql);
    if ($rs) {
      if ($lock = mysql_fetch_assoc($rs)) {
        return $lock['element_id'];        
      }
    } else {
      trigger_error("Can't find zone element ".$sql." ".mysql_error());
    }    
  }  
  
  /**
   * 
   * Create root zone element
   * @param int $zoneId
   * @param int $languageId
   */
	public static function createRootZoneElement($zoneId, $languageId){
	  $sql = "insert into `".DB_PREF."content_element` set visible = 1";
		$rs = mysql_query($sql);
    if($rs){
			$sql2 = "insert into `".DB_PREF."zone_to_content` set 
			language_id = '".mysql_real_escape_string($languageId)."',
			zone_id = '".$zoneId."',
			element_id = '".mysql_insert_id()."'";
			$rs2 = mysql_query($sql2);
			if(!$rs2)
				trigger_error($sql2." ".mysql_error());
		}
	}  
	
	
  /**
   * 
   * Get element children
   * @param int $elementId
   * @return array
   */
  public static function contentElementChildren($elementId){
    $sql = "select row_number, id, button_title, visible from `".DB_PREF."content_element` where parent= '".$elementId."' order by row_number";
    $rs = mysql_query($sql);
    if($rs){
      $elements = array();
      while($lock = mysql_fetch_assoc($rs)){
        $elements[] = $lock;
      }
      return $elements;
    }else trigger_error("Can't get content element children ".$sql." ".mysql_error());    
  }	
  
  /**
   * 
   * Update page
   * @param int $elementId
   * @param array $params
   */
  public static function updateContentElement($elementId, $params){
    $values = '';
    
    if (isset($params['buttonTitle']))
      $values .= 'button_title = \''.mysql_real_escape_string($params['buttonTitle']).'\'';

    if (isset($params['pageTitle']))
      $values .= ', page_title = \''.mysql_real_escape_string($params['pageTitle']).'\'';

    if (isset($params['keywords']))
      $values .= ', keywords = \''.mysql_real_escape_string($params['keywords']).'\'';

    if (isset($params['description']))
      $values .= ', description = \''.mysql_real_escape_string($params['description']).'\'';

    if (isset($params['url']))
      $values .= ', url= \''.mysql_real_escape_string($params['url']).'\'';

    if (isset($params['createdOn']))
      $values .= ', created_on = \''.mysql_real_escape_string($params['createdOn']).'\'';

    if (isset($params['lastModified']))
      $values .= ', last_modified= \''.mysql_real_escape_string($params['lastModified']).'\'';

    if (isset($params['type']))
      $values .= ', type = \''.mysql_real_escape_string($params['type']).'\'';

    if (isset($params['redirectURL']))
      $values .= ', redirect_url = \''.mysql_real_escape_string($params['redirectURL']).'\'';

    if (isset($params['visible']))
      $values .= ', visible = \''.mysql_real_escape_string($params['visible']).'\'';

    if (isset($params['rss']))
      $values .= ', rss = \''.mysql_real_escape_string($params['rss']).'\'';

    $sql = 'UPDATE `'.DB_PREF.'content_element` SET '.$values.' WHERE `id` = '.(int)$elementId.' ';
    $rs = mysql_query($sql);
    if ($rs) {
      return true;
    } else {
      trigger_error($sql.' '.mysql_error());
      return false;
    }
  }  
  
  /**
   * 
   * Insert new page
   * @param int $parentId
   * @param array $params
   */
  public static function insertContentElement($parentId, $params){
    $values = '';
    
    $values .= ' parent = '.(int)$parentId;
    $values .= ', row_number = '.((int)self::getMaxIndex($parentId) + 1);
    
    if (isset($params['buttonTitle']))
      $values .= ', button_title = \''.mysql_real_escape_string($params['buttonTitle']).'\'';

    if (isset($params['pageTitle']))
      $values .= ', page_title = \''.mysql_real_escape_string($params['pageTitle']).'\'';

    if (isset($params['keywords']))
      $values .= ', keywords = \''.mysql_real_escape_string($params['keywords']).'\'';

    if (isset($params['description']))
      $values .= ', description = \''.mysql_real_escape_string($params['description']).'\'';

    if (isset($params['url']))
      $values .= ', url= \''.mysql_real_escape_string($params['url']).'\'';

    if (isset($params['createdOn']))
      $values .= ', created_on = \''.mysql_real_escape_string($params['createdOn']).'\'';

    if (isset($params['lastModified']))
      $values .= ', last_modified= \''.mysql_real_escape_string($params['lastModified']).'\'';

    if (isset($params['type']))
      $values .= ', type = \''.mysql_real_escape_string($params['type']).'\'';

    if (isset($params['redirectURL']))
      $values .= ', redirect_url = \''.mysql_real_escape_string($params['redirectURL']).'\'';

    if (isset($params['visible']))
      $values .= ', visible = \''.mysql_real_escape_string($params['visible']).'\'';

    if (isset($params['rss']))
      $values .= ', rss = \''.mysql_real_escape_string($params['rss']).'\'';

    $sql = 'INSERT INTO `'.DB_PREF.'content_element` SET '.$values.' ';

    $rs = mysql_query($sql);
    if ($rs) {
      return mysql_insert_id();
    } else {
      trigger_error($sql.' '.mysql_error());
      return false;
    }
  }    
  
  
  public static function getMaxIndex($parentId) {
    $sql = "SELECT MAX(`row_number`) AS 'max_row_number' FROM `".DB_PREF."content_element` WHERE `parent` = '.(int)$parentId.' ";
    $rs = mysql_query($sql);
    if ($rs) {
      if ($lock = mysql_fetch_assoc($rs)) {
        return $lock['max_row_number'];
      } else {
        return false;
      }
      return mysql_insert_id();
    } else {
      trigger_error($sql.' '.mysql_error());
      return false;
    }
  }
  
  /**
   * @param string $url
   * @param int $allowed_id
   * @returns bool true if url is available ignoring $allowed_id page.
   */
  public static function availableUrl($url, $allowedId = null){
    if($allowedId)
      $sql = "select url from `".DB_PREF."content_element` where url = '".mysql_real_escape_string($url)."' and id <> '".$allowedId."'";
    else
      $sql = "select url from `".DB_PREF."content_element` where url = '".mysql_real_escape_string($url)."' ";
      
    $rs = mysql_query($sql);
    if(!$rs)
      trigger_error("Available url check ".$sql." ".mysql_error());

    if(mysql_num_rows($rs) > 0)
      return false;
    else
      return true;
  }
  
  
  /**
   * 
   * Create unique URL
   * @param string $url
   * @param int $allowed_id
   */
  public static function makeUrl($url, $allowed_id = null){
    require_once(BASE_DIR.LIBRARY_DIR.'php/text/transliteration.php');
    if($url == '')
      $url = 'page';
    $url = mb_strtolower($url);
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
    $url = str_replace(".", "-", $url);
    $url = str_replace("'", "", $url);
    $url = str_replace("?", "", $url);
    $url = str_replace(":", "", $url);
    $url = str_replace(";", "", $url);
    
    if($url == ''){
      $url = '-';
    }
      
    
    while($url != str_replace("--", "-", $url))
      $url = str_replace("--", "-", $url);
    
    if(self::availableUrl($url, $allowed_id))
      return $url;
      
    $i = 1;
    while(!self::availableUrl($url.'-'.$i, $allowed_id)){
      $i++;
    }
    
    return $url.'-'.$i;
  }    
  
}