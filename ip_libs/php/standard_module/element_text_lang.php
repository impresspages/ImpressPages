<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Library\Php\StandardModule;
 
if (!defined('BACKEND')) exit;
class element_text_lang extends Element{ //data element in area
  var $default_value;
  var $translation_field;
  var $mem_value;
  function print_field_new($prefix, $parent_id = null, $area = null){
    $html = new std_mod_html_output();
    global $std_mod_db;


        
		$languages = $std_mod_db->languages();
		
		$answer = '';
		foreach($languages as $key => $language){
			if(isset($this->mem_value))
				$html->input($prefix.'_'.$language['id'], $this->mem_value[$language['id']]);                      
			else
				$html->input($prefix.'_'.$language['id'], $this->default_value);                      
		}
  
    

    return $html->html;      
  }
  
 
   function memorize($prefix){
    global $std_mod_db;
     $languages = $std_mod_db->languages();
    unset($this->mem_value);
    foreach($languages as $key => $language){
      $this->mem_value[$language['id']] = $_REQUEST[$prefix.'_'.$language['id']]; 
    }
  }
  
  function reset(){
    unset($this->mem_value);
  }
 
  function print_field_update($prefix, $parent_id, $area){
    $html = new std_mod_html_output();
    global $std_mod_db;
 
    $answer = '';
    $sql2 = "select t.translation, l.d_short, t.id as t_id, l.id as l_id from ".DB_PREF."translation t, ".DB_PREF."language l where t.language_id = l.id and t.".$this->translation_field." = '".$parent_id."' ";
    $rs2 = mysql_query($sql2);
    if (!$rs2)
      trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
    else{
      $values = array();
      while($lock2 = mysql_fetch_assoc($rs2)){
        $values[$lock2['l_id']] = $lock2['translation'];          
      }
        
      $languages = $std_mod_db->languages();
      
      $answer .= '';


      foreach($languages as $key => $language){
        $sql3 = "select t.translation from ".DB_PREF."translation t, ".DB_PREF."language l where l.id = '".$language['id']."' and t.language_id = l.id and t.".$this->translation_field." = '".$parent_id."' ";
        $rs3 = mysql_query($sql3);
        $value='';
        if($rs3){
          if($lock3 = mysql_fetch_assoc($rs3))
            $value = $lock3['translation'];            
        }else trigger_error("Can't get all languages ".$sql3." ".mysql_error());
        $html->input($prefix.'_'.$language['id'], $value);                      
      }
  
    }

    return $html->html;  
  }


  function get_parameters($action, $prefix){
    return false;
     
  }


  function preview_value($value){
    global $std_mod_db;
    $languages = $std_mod_db->languages();
    $answer='';
    foreach($languages as $key => $language){
      $sql3 = "select t.translation from ".DB_PREF."translation t, ".DB_PREF."language l where l.id = '".$language['id']."' and t.language_id = l.id and t.".$this->translation_field." = '".$value."' ";
      $rs3 = mysql_query($sql3);
      if($rs3){
        if($lock3 = mysql_fetch_assoc($rs3))
          $answer .= '/'.$lock3['translation'];            
      }else trigger_error("Can't get all languages ".$sql3." ".mysql_error());
    }
    return htmlspecialchars($answer);
  }

  function check_field($prefix, $action){
    return null;
  }

  function process_insert($prefix, $area, $last_insert_id){
    global $std_mod_db;  
    $languages = $std_mod_db->languages();
    
    $sql2 = "delete from ".DB_PREF."translation where ".$this->translation_field." = '".$last_insert_id."' ";
    $rs2 = mysql_query($sql2);
    if($rs2){
      foreach($languages as $key => $language){
        $sql3 = "insert into ".DB_PREF."translation set translation = '".mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', language_id = '".$language['id']."', ".$this->translation_field." = '".$last_insert_id."' ";
        $rs3 = mysql_query($sql3);
        if(!$rs3)
          trigger_error("Can't insert language field values ".$sql3." ".mysql_error());            
      }
    }else
      trigger_error("Can't update parameter ".$sql2." ".mysql_error());            
  }
  function process_update($prefix, $area, $row_id){
    global $std_mod_db;  
    $languages = $std_mod_db->languages();
    
    $sql2 = "delete from ".DB_PREF."translation where ".$this->translation_field." = '".$row_id."' ";
    $rs2 = mysql_query($sql2);
    if($rs2){
      foreach($languages as $key => $language){
        $sql3 = "insert into ".DB_PREF."translation set translation = '".mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', language_id = '".$language['id']."', ".$this->translation_field." = '".$row_id."' ";
        $rs3 = mysql_query($sql3);
        if(!$rs3)
          trigger_error("Can't update language field values ".$sql3." ".mysql_error());            
      }
    }else
      trigger_error("Can't update parameter ".$sql2." ".mysql_error());            
  }
  function process_delete($area, $key){
    $sql2 = "delete from ".DB_PREF."translation where ".$this->translation_field." = '".$key."' ";
    $rs2 = mysql_query($sql2);
    if(!$rs2)
      trigger_error("Can't delete language field values ".$sql2." ".mysql_error());            
  }

  function print_search_field($level, $key  ){
   /* if (isset($_REQUEST['search_'.$key]))
      $value = $_REQUEST['search_'.$key];
    else
      $value = '';
    return '<input name="search_'.$key.'" value="'.htmlspecialchars($value).'" />';*/
  }

  function get_filter_option($value){
    /*return " ".$this->db_field." like '%".mysql_real_escape_string($value)."%' ";*/
  }

  function set_default_value($default_value){
     $this->default_value = $default_value;
  }
  function get_default_value(){
     return $this->default_value;
  }






}

