<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management;  

if (!defined('CMS')) exit; 

require_once (BASE_DIR.LIBRARY_DIR.'php/text/transliteration.php');

class Db {




  public static function getMenuModModule($id=null, $groupName = null, $moduleName = null) {
    if($id != null) {
      $sql = "select m.id, g.name as g_name, m.name as m_name from `".DB_PREF."content_module_group` g, `".DB_PREF."content_module` m where m.id = '".mysql_real_escape_string($id)."' and  m.group_id = g.id order by g.row_number, m.row_number limit 1";
    }elseif($groupName != null && $moduleName != null)
      $sql = "select m.id, g.name as g_name, m.name as m_name from `".DB_PREF."content_module_group` g, `".DB_PREF."content_module` m where g.name = '".mysql_real_escape_string($groupName)."' and m.group_id = g.id and m.name= '".mysql_real_escape_string($moduleName)."' order by g.row_number, m.row_number limit 1";
    else
      $sql = "select m.id, g.name as g_name, m.name as m_name from `".DB_PREF."content_module_group` g, `".DB_PREF."content_module` m where m.group_id = g.id order by g.row_number, m.row_number limit 1";
    $rs = mysql_query($sql);
    $answer = null;
    if($rs) {
      if($lock = mysql_fetch_assoc($rs))
        $answer = $lock;
    }else trigger_error($sql." ".mysql_error());
    return $answer;

  }


  public static function correctRowNumbers($parent) {
    global $globalWorker;
    $answer = true;
    $cur_number = 0;
    $sql = "select row_number from `".DB_PREF."content_element` where parent = '".mysql_real_escape_string($parent)."' order by row_number";
    $rs = mysql_query($sql);
    if($rs) {
      while($lock = mysql_fetch_assoc($rs)) {
        if($lock['row_number'] != $cur_number)
          $answer = false;
        $cur_number++;
      }
    }else
      $globalWorker->set_error($sql." ".mysql_error());


    return $answer;
  }


  public static function languages() {
    $answer = array();
    $sql = "select id, d_long, d_short from `".DB_PREF."language` where 1 order by row_number  ";
    $rs = mysql_query($sql);
    if($rs) {
      while($lock = mysql_fetch_assoc($rs))
        $answer[] = $lock;
    }else trigger_error($sql." ".mysql_error());
    return $answer;
  }





  public static function rootMenuElement($menuId, $language_id) { //returns root element of menu
    $sql = "select mte.element_id from `".DB_PREF."zone_to_content` mte where mte.language_id = '".$language_id."' and zone_id = '".$menuId."' ";
    $rs = mysql_query($sql);
    if($rs) {
      if($lock = mysql_fetch_assoc($rs)) {
        return $lock['element_id'];
      }
    }else
      trigger_error("Can't find zone element ".$sql." ".mysql_error());
  }


  public static function urlsByRootMenuElement($rootEelement) {
    $sql = "select l.url as lang_url, mp.url as zone_url from
    `".DB_PREF."zone_to_content` mte, `".DB_PREF."language` l, `".DB_PREF."zone` m, `".DB_PREF."zone_parameter` mp 
    where l.id = mp.language_id and mp.language_id = mte.language_id and 
    mte.element_id = '".mysql_real_escape_string($rootEelement)."' and 
    mte.zone_id = mp.zone_id and mp.zone_id = m.id ";
    $rs = mysql_query($sql);
    if($rs) {
      if($lock = mysql_fetch_assoc($rs)) {
        return $lock;
      }
    }else
      trigger_error("Can't find menu element ".$sql." ".mysql_error());
  }



  public static function menuElement($id) { //returns element
    $sql = "select * from `".DB_PREF."content_element` where id = '".$id."' ";
    $rs = mysql_query($sql);
    if($rs) {
      if($lock = mysql_fetch_assoc($rs)) {
        return $lock;
      }
    }else
      trigger_error("Can't find menu element ".$sql." ".mysql_error());
  }



  public static function menuElementChildren($element_id) {
    $sql = "select row_number, id, page_title, visible from `".DB_PREF."content_element` where parent= '".$element_id."' order by row_number";
    $rs = mysql_query($sql);
    if($rs) {
      $elements = array();
      while($lock = mysql_fetch_assoc($rs)) {
        $elements[] = $lock;
      }
      return $elements;
    }else trigger_error("Can't get content element children ".$sql." ".mysql_error());

  }


  public static function menuElementParagraphs($elementId) {
    $sql = "select * from `".DB_PREF."content_element_to_modules` where element_id = '".$elementId."'";
    $rs = mysql_query($sql);
    if($rs) {
      $elements = array();
      while($lock = mysql_fetch_assoc($rs)) {
        $elements[] = $lock;
      }
      return $elements;
    }else trigger_error("Can't get content element children ".$sql." ".mysql_error());

  }


  public static function createMenuElement($menuId, $languageId) {
    global $parametersMod;
    if(Db::menuElement($menuId, $languageId) == null) {

      if($parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages'))
        $visible = '0';
      else
        $visible = '1';

      $sql = "insert into `".DB_PREF."content_element` set button_title = '', visible= ".$visible.", html='', last_modified= CURRENT_TIMESTAMP";
      $rs = mysql_query($sql);
      if($rs) {
        $elementId = mysql_insert_id();
        $sql = "insert into `".DB_PREF."zone_to_content` set language_id = '".$languageId."', zone_id = '".$menuId."', element_id = '".$elementId."' ";
        $rs = mysql_query($sql);
        if($rs) {
          return $elementId;
        }else
          trigger_error("Can't bind element to zone ".$sql." ".mysql_error());
      }else {
        trigger_error("Can't create new element ".$sql." ".mysql_error());
      }

    }


  }





  public static function insertMenuElement($parent, $row_number, $button_title, $page_title = '', $keywords = '', $description = '', $url = '', $rss=0, $visible = null) {
    global $parametersMod;
    global $globalWorker;
    if($page_title == '')
      $page_title = $button_title;
    $sql = "update `".DB_PREF."content_element` set  row_number = row_number + 1 where parent = '".$parent."' and row_number >= ".$row_number." ";
    if (!mysql_query($sql))
      $globalWorker->set_error("Cant update row number ".$sql);

    if($visible === null) {
      if($parametersMod->getValue('standard', 'menu_management', 'options', 'hide_new_pages'))
        $visible = '0';
      else
        $visible = '1';
    }
    $sql = "insert into `".DB_PREF."content_element` set last_modified= CURRENT_TIMESTAMP, parent = '".$parent."',row_number = '".$row_number."', button_title='".mysql_real_escape_string($button_title)."', page_title='".mysql_real_escape_string($page_title)."', keywords='', description='', url='".mysql_real_escape_string(Db::makeUrl($page_title))."', rss='".mysql_real_escape_string($rss)."', visible='".$visible."'";
    if (!mysql_query($sql))
      trigger_error("Can't insert new page ".$sql." ".mysql_error());
    else
      return(mysql_insert_id());

  }


  public static function deleteMenuElement($id) {
    global $globalWorker;
    $sql = "delete from `".DB_PREF."content_element` where id = '".$id."' ";
    if (!mysql_query($sql))
      $globalWorker->set_error("Can't delete element ".$sql." ".mysql_error());

  }

  public static function showMenuElement($id) {
    global $globalWorker;
    $sql = "update `".DB_PREF."content_element` set visible = 1 where id = '".$id."' ";
    $rs = mysql_query($sql);
    if (!$rs)
      $globalWorker->set_error("Can't change page visibility setting ".$sql);
  }



  public static function renameContentElement($id, $title) {
    global $globalWorker;
    $sql = "update `".DB_PREF."content_element` set button_title = '".mysql_real_escape_string($title)."' where id = '".$id."' ";
    if (!mysql_query($sql))
      $globalWorker->set_error("Can't rename element ".$sql." ".mysql_error());
  }

  public static function hideMenuElement($id) {
    global $globalWorker;
    $sql = "update `".DB_PREF."content_element` set visible = 0 where id = '".$id."' ";
    $rs = mysql_query($sql);
    if (!$rs)
      $globalWorker->set_error("Can't change page visibility setting ".$sql);
  }

  public static function changeMenuElementRowNumbers($parent, $index, $difference, $compare) {
    global $globalWorker;
    $sql = "update `".DB_PREF."content_element` set row_number = row_number + '".$difference."' where parent = '".$parent."' and row_number ".$compare." '".$index."' ";
    if (!mysql_query($sql))
      $globalWorker->set_error("Cant update row number ".$sql);


  }


  public static function moveMenuElement($id, $parent, $index) {
    global $globalWorker;
    $sql = "update `".DB_PREF."content_element` set row_number = '".$index."', parent = '".$parent."' where id = '".$id."' ";
    if (!mysql_query($sql))
      $globalWorker->set_error("Cant update content element ".$sql);
  }



  public static function menuModules() {
    $sql = "select g.translation as group_translation, g.id as group_id, m.translation as module_translation, m.id as module_id, g.name as group_name, m.name as module_name
     from `".DB_PREF."content_module` m, `".DB_PREF."content_module_group` g
      where m.group_id = g.id order by g.row_number, m.row_number";
    $rs = mysql_query($sql);
    if($rs) {
      $groups = array();
      while($lock = mysql_fetch_assoc($rs)) {
        if(!isset($groups[$lock['group_translation']])) {
          $groups[$lock['group_translation']] = array();
        }
        $groups[$lock['group_translation']][] = $lock;
      }
      return $groups;
    }else trigger_error("Can't get content modules ".$sql." ".mysql_error());

  }

  public static function pageModules($elementId) {
    $sql = "select mg.name as group_name, m.name, etm.module_key, etm.visible, etm.module_key as module_name, etm.module_id as instance_id
     from  `".DB_PREF."content_element_to_modules` etm, 
      `".DB_PREF."content_module` m,
       `".DB_PREF."content_module_group` mg
    where etm.element_id = '".$elementId."'
    and etm.module_key = m.name    
    and mg.id = m.group_id
    order by etm.row_number  ";  

    $rs = mysql_query($sql);
    if($rs) {
      $modules = array();
      while($lock = mysql_fetch_assoc($rs)) {
        $modules[] = $lock;
      }
      return $modules;
    }else trigger_error("Can't get content modules ".$sql." ".mysql_error());

  }


  public static function getModuleId($group, $name) {
    $sql = "select m.id from `".DB_PREF."content_module` m, `".DB_PREF."content_module_group` g where m.group_id = g.id and g.name= '".mysql_real_escape_string($group)."' and m.name= '".mysql_real_escape_string($name)."' ";
    $rs = mysql_query($sql);
    if($rs) {
      $lock = mysql_fetch_assoc($rs);
      if($lock)
        return $lock['id'];
      else
        return false;
    }else
      trigger_error("Can't get content module ".$sql." ".mysql_error());
  }





  public static function availableUrl($url, $allowed_id) {
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

  public static function makeUrl($url, $allowed_id = null) {
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
    $url = str_replace("?", "-", $url);
    $url = str_replace(":", "", $url);
    $url = str_replace(";", "", $url);


    while($url != str_replace("--", "-", $url))
      $url = str_replace("--", "-", $url);

    if(Db::availableUrl($url, $allowed_id))
      return $url;

    $i = 1;
    while(!Db::availableUrl($url.'-'.$i, $allowed_id)) {
      $i++;
    }

    return $url.'-'.$i;
  }

  public static function getRealElements() { //real - means real pages. Root elements, that are used to link menu tree to menu are skiped.
    $sql = "select id from `".DB_PREF."content_element` where parent is not null ";
    $rs = mysql_query($sql);
    $answer = array();
    if($rs) {
      while($lock = mysql_fetch_assoc($rs))
        $answer[] = $lock['id'];
      return $answer;
    }else
      trigger_error($sql." ".mysql_error());
    return false;
  }

  public static function removeZoneToContent($zoneId, $languageId) {
    $sql = "delete from ".DB_PREF."zone_to_content where `language_id` = '".(int)$languageId."' and `zone_id` = '".(int)$zoneId."' ";
    $rs = mysql_query($sql);
    if(!$rs) {
      trigger_error($sql.' '.mysql_error());
    }
  }
}

