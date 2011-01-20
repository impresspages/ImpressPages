<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;

if (!defined('CMS')) exit; 

class Db{
	
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
  
  public static function getContentModModule($id=null, $groupName = null, $moduleName = null){
    if($id != null)
      $sql = "select m.id, g.name as g_name, m.name as m_name from `".DB_PREF."content_module_group` g, `".DB_PREF."content_module` m where m.id = '".mysql_real_escape_string($id)."' and  m.group_id = g.id order by g.row_number, m.row_number limit 1";
    elseif($groupName != null && $moduleName != null)
      $sql = "select m.id, g.name as g_name, m.name as m_name from `".DB_PREF."content_module_group` g, `".DB_PREF."content_module` m where g.name = '".mysql_real_escape_string($groupName)."' and m.group_id = g.id and m.name= '".mysql_real_escape_string($moduleName)."' order by g.row_number, m.row_number limit 1";
    else      
      $sql = "select m.id, g.name as g_name, m.name as m_name from `".DB_PREF."content_module_group` g, `".DB_PREF."content_module` m where m.group_id = g.id order by g.row_number, m.row_number limit 1";
    $rs = mysql_query($sql);
    $answer = null;
    if($rs){
      if($lock = mysql_fetch_assoc($rs))      
        $answer = $lock;
    }else trigger_error($sql." ".mysql_error());
    return $answer;
  
  }  


    public static function languages(){
      $answer = array();
      $sql = "select id, d_long, d_short from `".DB_PREF."language` where 1 order by row_number  ";
      $rs = mysql_query($sql);
      if($rs){
        while($lock = mysql_fetch_assoc($rs))
          $answer[$lock['id']] = $lock;
      }else trigger_error($sql." ".mysql_error());
      return $answer;
    }



  
  public static function zones(){
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
      
      
  public static function rootContentElement($contentElementId, $languageId){ //returns root element of content
    $sql = "select mte.element_id from `".DB_PREF."zone_to_content` mte, `".DB_PREF."language` l where l.id = '".$languageId."' and  mte.language_id = l.id and zone_id = '".$contentElementId."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock['element_id'];        
      }
    }else
      trigger_error("Can't find zone element ".$sql." ".mysql_error());    
  }
  
  public static function urlsByRootContentElement($rootElement){ //returns root element of content
    $sql = "select l.url as lang_url, mp.url as zone_url from 
    `".DB_PREF."zone_to_content` mte, `".DB_PREF."language` l, `".DB_PREF."zone` m, `".DB_PREF."zone_parameter` mp 
    where l.id = mp.language_id and mp.language_id = mte.language_id and 
    mte.element_id = '".mysql_real_escape_string($rootElement)."' and 
    mte.zone_id = mp.zone_id and mp.zone_id = m.id ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;        
      }
    }else
      trigger_error("Can't find zone element ".$sql." ".mysql_error());    
  }
    
  
  
  public static function contentElement($id){ //returns element
    $sql = "select * from `".DB_PREF."content_element` where id = '".$id."' ";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        return $lock;        
      }
    }else
      trigger_error("Can't find content element ".$sql." ".mysql_error());    
  }
  
  
  
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
  
  
  public static function contentElementParagraphs($elementId){
    $sql = "select * from `".DB_PREF."content_element_to_modules` where element_id = '".$elementId."'";
    $rs = mysql_query($sql);
    if($rs){
      $elements = array();
      while($lock = mysql_fetch_assoc($rs)){
        $elements[] = $lock;
      }
      return $elements;
    }else trigger_error("Can't get content element children ".$sql." ".mysql_error());
  
  }
  
 
  
  public static function renameContentElement($id, $title){    
   $sql = "update `".DB_PREF."content_element` set title = '".mysql_real_escape_string($title)."' where id = '".$id."' ";
   if (!mysql_query($sql)) 
      set_error("Can't rename element ".$sql." ".mysql_error());
  }
	
  public static function deleteContentElement($id){    
   $sql = "delete from `".DB_PREF."content_element` where id = '".$id."' ";
   if (!mysql_query($sql)) 
      set_error("Can't delete element ".$sql." ".mysql_error());
  
  }
  
  public static function showContentElement($id){    
    $sql = "update `".DB_PREF."content_element` set visible = 1 where id = '".$id."' ";
    $rs = mysql_query($sql);
    if (!$rs)
      set_error("Can't change page visibility setting ".$sql);
  }
  
  
  public static function hideContentElement($id){    
    $sql = "update `".DB_PREF."content_element` set visible = 0 where id = '".$id."' ";
    $rs = mysql_query($sql);
    if (!$rs)
      set_error("Can't change page visibility setting ".$sql);
  }
      
  function changeContentElementRowNumbers($parent, $index, $difference, $compare){
    $sql = "update `".DB_PREF."content_element` set row_number = row_number + '".$difference."' where parent = '".$parent."' and row_number ".$compare." '".$index."' ";
    if (!mysql_query($sql))
      set_error("Cant update row number ".$sql);
  
  
  }
  
      
  public static function moveContentElement($id, $parent, $index){
    $sql = "update `".DB_PREF."content_element` set row_number = '".$index."', parent = '".$parent."' where id = '".$id."' ";
    if (!mysql_query($sql))
      set_error("Cant update content element ".$sql);
  }    
  
   
	public static function correctRowNumbers($parent){
		$answer = true;
		$cur_number = 0;
    $sql = "select row_number from `".DB_PREF."content_element` where parent = '".mysql_real_escape_string($parent)."' order by row_number";
		$rs = mysql_query($sql);
    if($rs){
			while($lock = mysql_fetch_assoc($rs)){
				if($lock['row_number'] != $cur_number)
					$answer = false;
				
			}				
		}else
			set_error($sql." ".mysql_error());
		
		
		return false;
	}
  
  public static function contentModules($language){
    $sql = "select tg.translation as group_translation, g.id as group_id, tm.translation as module_translation, m.id as module_id, g.name as group_name, m.name as module_name 
     from `".DB_PREF."content_module` m, `".DB_PREF."content_module_group` g, `".DB_PREF."cms_translation` tg, `".DB_PREF."cms_translation` tm
      where m.group_id = g.id and tg.content_module_group_id = g.id and tm.content_module_id = m.id
      and tg.language_id = '".$language."' and tm.language_id = '".$language."'  
      order by g.row_number, m.row_number";
    $rs = mysql_query($sql);
    if($rs){
      $groups = array();
      while($lock = mysql_fetch_assoc($rs)){
        if(!isset($groups[$lock['group_translation']])){
          $groups[$lock['group_translation']] = array();
        }
        $groups[$lock['group_translation']][] = $lock;
      }
      return $groups;
    }else trigger_error("Can't get content modules ".$sql." ".mysql_error());
  
  }
  
  public static function pageModules($elementId){
    $sql = "select mg.name as group_name, m.name, etm.module_key, etm.visible, etm.module_key as module_name, etm.module_id as instance_id 
     from  `".DB_PREF."content_element_to_modules` etm, 
      `".DB_PREF."content_module` m,
       `".DB_PREF."content_module_group` mg
    where etm.element_id = '".$elementId."'
    and etm.module_key = m.name    
    and mg.id = m.group_id
    order by etm.row_number  ";  
    
    $rs = mysql_query($sql);
    if($rs){
      $modules = array();
      while($lock = mysql_fetch_assoc($rs)){
        $modules[] = $lock;
      }
      return $modules;
    }else trigger_error("Can't get content modules ".$sql." ".mysql_error());    
  
  }
  
  public static function getModuleGroup($moduleKey){
    $sql = "select mg.name from `".DB_PREF."content_module_group` mg, `".DB_PREF."content_module` m where m.group_id = mg.id and m.name = '".mysql_real_escape_string($moduleKey)."'";
    $rs = mysql_query($sql);
    if($rs){
      if(mysql_num_rows($rs) != 1)
        return null;
      else{
        if($lock = mysql_fetch_assoc($rs))
          return $lock['name'];
        else
          return null;
      }
    }else trigger_error("Can't get module group ".$sql." ".mysql_error());    
  }
  
  
  public static function getModuleId($group, $name){
    $sql = "select m.id from `".DB_PREF."content_module` m, `".DB_PREF."content_module_group` g where m.group_id = g.id and g.name= '".mysql_real_escape_string($group)."' and m.name= '".mysql_real_escape_string($name)."' ";
    $rs = mysql_query($sql);
    if($rs){
      $lock = mysql_fetch_assoc($rs);
      if($lock)
        return $lock['id'];
      else
        return false;
    }else
      trigger_error("Can't get content module ".$sql." ".mysql_error());  
  }
  

  
  public static function updateContentElement($elementId, $params){
    $values = '';
    
    if (isset($params['button_title']))
      $values .= 'button_title = \''.mysql_real_escape_string($params['button_title']).'\'';

    if (isset($params['page_title']))
      $values .= ',page_title = \''.mysql_real_escape_string($params['page_title']).'\'';

    if (isset($params['keywords']))
      $values .= ', keywords = \''.mysql_real_escape_string($params['keywords']).'\'';

    if (isset($params['description']))
      $values .= ',description = \''.mysql_real_escape_string($params['description']).'\'';

    if (isset($params['url']))
      $values .= ', url= \''.mysql_real_escape_string($params['url']).'\'';

    if (isset($params['created_on']))
      $values .= ',created_on = \''.mysql_real_escape_string($params['created_on']).'\'';

    if (isset($params['last_modified']))
      $values .= ', last_modified= \''.mysql_real_escape_string($params['last_modified']).'\'';

    if (isset($params['type']))
      $values .= ',type = \''.mysql_real_escape_string($params['type']).'\'';

    if (isset($params['redirect_url']))
      $values .= ',redirect_url = \''.mysql_real_escape_string($params['redirect_url']).'\'';

    if (isset($params['visible']))
      $values .= ',visible = \''.mysql_real_escape_string($params['visible']).'\'';

    if (isset($params['rss']))
      $values .= ',rss = \''.mysql_real_escape_string($params['rss']).'\'';

    $sql = 'UPDATE `'.DB_PREF.'content_element` SET '.$values.' WHERE `id` = '.(int)$elementId.' ';
    $rs = mysql_query($sql);
    if ($rs) {
      return true;
    } else {
      trigger_error($sql.' '.mysql_error());
      return false;
    }
  }
  
  public static function insertContentElement($parentId, $index, $visible, $params){
    $values = '';
    
    $values .= ' parent = '.(int)$parentId;
    $values .= ', row_number = '.(int)$index;
    $values .= ', visible = '.(int)$visible;
    
    if (isset($params['button_title']))
      $values .= ',button_title = \''.mysql_real_escape_string($params['button_title']).'\'';

    if (isset($params['page_title']))
      $values .= ',page_title = \''.mysql_real_escape_string($params['page_title']).'\'';

    if (isset($params['keywords']))
      $values .= ', keywords = \''.mysql_real_escape_string($params['keywords']).'\'';

    if (isset($params['description']))
      $values .= ',description = \''.mysql_real_escape_string($params['description']).'\'';

    if (isset($params['url']))
      $values .= ', url= \''.mysql_real_escape_string($params['url']).'\'';

    if (isset($params['created_on']))
      $values .= ',created_on = \''.mysql_real_escape_string($params['created_on']).'\'';

    if (isset($params['last_modified']))
      $values .= ', last_modified= \''.mysql_real_escape_string($params['last_modified']).'\'';

    if (isset($params['type']))
      $values .= ',type = \''.mysql_real_escape_string($params['type']).'\'';

    if (isset($params['redirect_url']))
      $values .= ',redirect_url = \''.mysql_real_escape_string($params['redirect_url']).'\'';


    if (isset($params['rss']))
      $values .= ',rss = \''.mysql_real_escape_string($params['rss']).'\'';

    $sql = 'INSERT INTO `'.DB_PREF.'content_element` SET '.$values.' ';
    $rs = mysql_query($sql);
    if ($rs) {
      return mysql_insert_id();
    } else {
      trigger_error($sql.' '.mysql_error());
      return false;
    }
  }  
  
  
  public static function availableUrl($url, $allowed_id = null){
    if($allowed_id)
      $sql = "select url from `".DB_PREF."content_element` where url = '".mysql_real_escape_string($url)."' and id <> '".$allowed_id."'";
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
  
  public static function makeUrl($url, $allowed_id = null){
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
    
    if($url == ''){
      $url = '-';
    }
      
    
    while($url != str_replace("--", "-", $url))
      $url = str_replace("--", "-", $url);
    
    if(Db::availableUrl($url, $allowed_id))
      return $url;
      
    $i = 1;
    while(!Db::availableUrl($url.'-'.$i, $allowed_id)){
      $i++;
    }
    
    return $url.'-'.$i;
  }  
}
   
   
