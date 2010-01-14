<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\developer\zones;


if (!defined('BACKEND')) exit;

  
require_once (BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once (__DIR__.'/db.php');


class ZonesArea extends \Library\Php\StandardModule\Area{
  function after_insert($id){
    global $parametersMod;
    Db::createRootZonesElement($id);
    $zone = Db::getZone($id);
    if($zone['associated_group'] == 'standard' && $zone['associated_module'] == 'content_management'){
      /* add menu management associated zones */
      $newZonesStr = $this->addToAssociatedZones($parametersMod->getValue('standard', 'menu_management', 'options', 'associated_zones'), $zone['name']);
      $parametersMod->setValue('standard', 'menu_management', 'options', 'associated_zones', $newZonesStr);
      
      $newZonesStr = $this->addToAssociatedZones($parametersMod->getValue('administrator', 'search', 'options', 'searchable_zones'), $zone['name']);
      $parametersMod->setValue('administrator', 'search', 'options', 'searchable_zones', $newZonesStr);
      
      $newZonesStr = $this->addToAssociatedZones($parametersMod->getValue('administrator', 'sitemap', 'options', 'associated_zones'), $zone['name']);
      $parametersMod->setValue('administrator', 'sitemap', 'options', 'associated_zones', $newZonesStr);
    }
    $newZonesStr = $this->addToAssociatedZones($parametersMod->getValue('standard', 'configuration', 'advanced_options', 'xml_sitemap_associated_zones'), $zone['name']);
    $parametersMod->setValue('standard', 'configuration', 'advanced_options', 'xml_sitemap_associated_zones', $newZonesStr);
  }
  
  function addToAssociatedZones($currentValue, $newZone, $depth = null){
      $associatedZonesStr = $currentValue;
      $associatedZones = explode("\n", $associatedZonesStr);
      $found = false;
      foreach($associatedZones as $key => $value){
        if($this->getModuleKey($value) == $newZone)
          $found = true;
      } 
      if(!$found){        
        if($associatedZonesStr == '')
          $associatedZonesStr = $this->makeZoneStr($newZone, $depth);
        else
          $associatedZonesStr .= "\n".$this->makeZoneStr($newZone, $depth);
        return $associatedZonesStr;
        
      }
  }


  function removeFromAssociatedZones($currentValue, $deletedZone){
    $associatedZones = explode("\n", $currentValue);
    $newStr = '';
    foreach($associatedZones as $key => $value){
      if($this->getModuleKey($value) != $deletedZone){
        if($newStr != '')
          $newStr .= "\n";
        $newStr .= $value;
      }
    }
    return $newStr;
  }


  function update_associated_zones($currentValue, $deletedZone, $oldName, $newName, $newDepth){
    $associatedZones = explode("\n", $currentValue);
    $newStr = '';
    foreach($associatedZones as $key => $value){
      if($this->getModuleKey($value) != $deletedZone){
        if($newStr != '')
          $newStr .= "\n";
        $newStr .= $value;
      }else{
        if($newStr != '')
          $newStr .= "\n";
        $newStr .= $this->makeZoneStr($newName, $newDepth);
      }
    }
    return $newStr;
  }

  
  function makeZoneStr($zoneName, $depth = null){
    if($depth !== null)
      return $zoneName.'['.$depth.']';
    else
      return $zoneName;
  }
  
  function getModuleKey($str){
	  $begin = strrpos($str, '[');
    $end =  strrpos($str, ']');
	  if($begin !== false && $end === strlen($str) - 1)
	   return substr($str, 0, $begin);
	  else
	   return $str;
  }
  
  function get_module_depth($str){
	  $begin = strrpos($str, '[');
    $end =  strrpos($str, ']');
	  if($begin !== false && $end === strlen($str) - 1)
	   return substr($str, $begin + 1, - 1);
	  else
	   return -1;
  
  }

  function before_delete($id){
    global $parametersMod;
    $zone = Db::getZone($id);
    //if($zone['associated_group'] == 'standard' && $zone['associated_module'] == 'content_management'){
      $associatedZonesStr = $this->removeFromAssociatedZones($parametersMod->getValue('standard', 'menu_management', 'options', 'associated_zones'), $zone['name']);
      $parametersMod->setValue('standard', 'menu_management', 'options', 'associated_zones', $associatedZonesStr);

      $associatedZonesStr = $this->removeFromAssociatedZones($parametersMod->getValue('administrator', 'search', 'options', 'searchable_zones'), $zone['name']);
      $parametersMod->setValue('administrator', 'search', 'options', 'searchable_zones', $associatedZonesStr);

      $associatedZonesStr = $this->removeFromAssociatedZones($parametersMod->getValue('administrator', 'sitemap', 'options', 'associated_zones'), $zone['name']);
      $parametersMod->setValue('administrator', 'sitemap', 'options', 'associated_zones', $associatedZonesStr);
    //}
    $newZonesStr = $this->removeFromAssociatedZones($parametersMod->getValue('standard', 'configuration', 'advanced_options', 'xml_sitemap_associated_zones'), $zone['name']);
    $parametersMod->setValue('standard', 'configuration', 'advanced_options', 'xml_sitemap_associated_zones',  $newZonesStr);
      
      
      Db::deleteParameters($id);
  }
  
  
  function before_update($id){
    global $parametersMod;
    $this->tmp_zone = Db::getZone($id);
  }

  function after_update($id){
    global $parametersMod;
    $zone = Db::getZone($id);
    
    
    $newZonesStr = $this->update_associated_zones($parametersMod->getValue('standard', 'menu_management', 'options', 'associated_zones'), $this->tmp_zone['name'], $zone['name']);
    $parametersMod->setValue('standard', 'menu_management', 'options', 'associated_zones', $newZonesStr);
    
    $newZonesStr = $this->update_associated_zones($parametersMod->getValue('administrator', 'search', 'options', 'searchable_zones'), $this->tmp_zone['name'], $zone['name']);
    $parametersMod->setValue('administrator', 'search', 'options', 'searchable_zones', $newZonesStr);
    
    $newZonesStr = $this->update_associated_zones($parametersMod->getValue('administrator', 'sitemap', 'options', 'associated_zones'), $this->tmp_zone['name'], $zone['name'], $zone['depth']);
    $parametersMod->setValue('administrator', 'sitemap', 'options', 'associated_zones', $newZonesStr);
    
    $newZonesStr = $this->update_associated_zones($parametersMod->getValue('standard', 'configuration', 'advanced_options', 'xml_sitemap_associated_zones'), $this->tmp_zone['name'], $zone['name'], $zone['depth']);
    $parametersMod->setValue('standard', 'configuration', 'advanced_options', 'xml_sitemap_associated_zones', $newZonesStr);
        

  }

}

class Manager{
   var $standardModule;   
   function __construct(){
     global $parametersMod;
     
     


     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'zones','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
		 $element->required = true;
		 $element->sortable = false;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'zones','admin_translations','key');
     $element->db_field = "name";
     $element->show_on_list = true;
		 $element->required = true;
		 $element->sortable = false;
     $elements[] = $element;

/*     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'zones','admin_translations','template');
     $element->db_field = "template";
     $element->show_on_list = true;
		 $element->required = true;
		 $element->sortable = false;
     $elements[] = $element;*/
     
     $element = new \Library\Php\StandardModule\element_select("select");
     $element->set_name($parametersMod->getValue('developer', 'zones','admin_translations','template'));
     $element->set_db_field("template");
     $element->required = true;

 
     $templates = $this->getAvailableTemplates();
     $values = array();
     $values[] = array("", "");
     foreach($templates as $key => $template){
       $value = array();
       $value[] = $template;
       $value[] = $template;
       $values[] = $value;
     }
     
     $element->set_values($values);
     
/*     $code='
        global $cms;
        
       $sql = " select translation from ".DB_PREF."module where id = \'".$value."\' ";
       $rs = mysql_query($sql);
       if ($rs && $lock = mysql_fetch_assoc($rs)){
          $value = htmlspecialchars(stripslashes($lock[\'translation\']));
       }else
        $value = \'\';
       
       if(!$rs)
        trigger_error("Can\'t get field value translation ".$sql); 
     ';
     $element->set_php_code_for_preview($code);*/

     $element->set_show_on_list(true);
     $elements[] = $element;     
     



/*     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'zones','admin_translations','depth');
     $element->db_field = "depth";
     $element->show_on_list = false;
     $element->default_value = 10;     
		 $element->required = true;
		 $element->sortable = false;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'zones','admin_translations','inactive_depth');
     $element->db_field = "inactive_depth";
     $element->show_on_list = false;
     $element->default_value = 0;     
		 $element->required = true;
		 $element->sortable = false;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_bool("text");
     $element->name = $parametersMod->getValue('developer', 'zones','admin_translations','inactive_if_parent');
     $element->db_field = "inactive_if_parent";
     $element->show_on_list = false;
     $element->default_value = 0;     
		 $element->required = true;
		 $element->sortable = false;
     $elements[] = $element;
*/
		 

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'zones','admin_translations','associated_group');
     $element->db_field = "associated_group";
     $element->show_on_list = true;
     $element->default_value = 'standard';     
		 $element->sortable = false;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'zones','admin_translations','associated_module');
     $element->db_field = "associated_module";
     $element->show_on_list = true;
     $element->default_value = 'content_management';     
		 $element->sortable = false;
     $elements[] = $element;


     $area0 = new ZonesArea();
     $area0->db_table = "zone";
     $area0->name = $parametersMod->getValue('developer', 'zones','admin_translations','zones');
     $area0->db_key = "id";
     $area0->elements = $elements; 
     $area0->sort_field = "row_number";
		 $area0->order_by = "row_number";
		 $area0->new_record_position = "bottom";
     $area0->sortable = true; 





     
     $this->standardModule = new \Library\Php\StandardModule\StandardModule($area0);
   }
   function manage(){
    return $this->standardModule->manage();
     
   }
  
   function getAvailableTemplates(){
    $answer = array();
    if(is_dir(THEME_DIR.THEME)){
      $handle = opendir(THEME_DIR.THEME);
      if($handle !== false){
         while (false !== ($file = readdir($handle))) {
          if(strtolower(substr($file, -4, 4)) == '.php' && file_exists(THEME_DIR.THEME.'/'.$file) && is_file(THEME_DIR.THEME.'/'.$file) && $file != '..' && $file != '.')
           $answer[$file] = $file;
         }
         return $answer;
      }
    }
   }
}
