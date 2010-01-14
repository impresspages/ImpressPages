<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Library\Php\StandardModule;
 
if (!defined('BACKEND')) exit;

require_once(LIBRARY_DIR.'php/standard_module/element_text_lang.php');
class element_select_lang extends element_text_lang{ //data element in area
  var $default_value;
  var $translation_field;
  var $mem_value;
  function print_field_new($prefix, $parent_id = null, $area = null){
  
    $answer = '';
  
//    $html = new std_mod_html_output();
    global $std_mod_db;


        
      $languages = $std_mod_db->languages();

    
    
    if (isset($this->mem_value))
      $value = $this->mem_value;
    else  
      $value = $this->default_value;

    $answer .= '<select class="stdMod" name="'.$prefix.'_tmp" >';
    if (isset($this->values) != null && sizeof($this->values) > 0)
    foreach($this->values as $key => $current_value){
      $str = '';
      foreach($current_value as $value_language){
        if($str != '')
          $str .= ' / ';
        $str .= $value_language[1];
      }
      if ($value_language[0] == $this->default_value || $value_language[0] == $value)
         $selected = " selected ";
      else
         $selected = ""; 
      $answer .= '<option value="'.htmlspecialchars($key).'" '.$selected.'>'.htmlspecialchars($str).'</option>';
      
    }
    $answer .= '</select>';  
    return $answer;    
  }
  
 
  function memorize($prefix){
      if(isset($_REQUEST[$prefix.'_tmp']))
        $this->mem_value = $_REQUEST[$prefix.'_tmp'];
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
      $value_exists = false;
      $answer .= '<select class="stdMod" name="'.$prefix.'_tmp" >';
      if (isset($this->values) != null && sizeof($this->values) > 0)
      foreach($this->values as $key => $current_value){
        $str = '';
        $selected_tmp = true;
        foreach($current_value as $key2 => $value_language){
          if($str != '')
            $str .= ' / ';
          $str .= $value_language[1];
          if(!isset($values[$key2]) || $value_language[0]!= $values[$key2]){
            $selected_tmp = false;
          }
        }
        if ($selected_tmp){
           $selected = " selected ";
          $value_exists = true;
        }else
           $selected = ""; 
        $answer .= '<option value="'.htmlspecialchars($key).'" '.$selected.'>'.htmlspecialchars($str).'</option>';
        
      }
      if(!$value_exists){
        $str = '';
        foreach($current_value as $key => $value_language){
          if($str != '')
            $str .= ' / ';
          if(isset($values[$key]))
            $str .= $values[$key];
        }
        $answer .= '<option value="'.sizeof($this->values).'" selected>'.htmlspecialchars($str).' (not in list)</option>';
      }
        
    }
    $answer .= '</select>';  
    return $answer;    
  }


  function process_insert($prefix, $area, $last_insert_id){
    global $std_mod_db;  
    $languages = $std_mod_db->languages();

    
    $sql2 = "delete from ".DB_PREF."translation where ".$this->translation_field." = '".$last_insert_id."' ";
    $rs2 = mysql_query($sql2);
    if($rs2){
      foreach($languages as $key => $language){
        $sql3 = "insert into ".DB_PREF."translation set translation = '".$this->values[$_REQUEST[$prefix.'_tmp']][$language['id']][0]."', language_id = '".$language['id']."', ".$this->translation_field." = '".$last_insert_id."' ";
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
    
    
    if($_REQUEST[$prefix.'_tmp'] == sizeof($this->values)){         
      return; //the value is not changed and dont match any of list.
    }
    $sql2 = "delete from ".DB_PREF."translation where ".$this->translation_field." = '".$row_id."' ";
    $rs2 = mysql_query($sql2);
    if($rs2){
      foreach($languages as $key => $language){
        $sql3 = "insert into ".DB_PREF."translation set translation = '".$this->values[$_REQUEST[$prefix.'_tmp']][$language['id']][0]."', language_id = '".$language['id']."', ".$this->translation_field." = '".$row_id."' ";
        $rs3 = mysql_query($sql3);
        if(!$rs3)
          trigger_error("Can't update language field values ".$sql3." ".mysql_error());            
      }
    }else
      trigger_error("Can't update parameter ".$sql2." ".mysql_error());            
  }

}

