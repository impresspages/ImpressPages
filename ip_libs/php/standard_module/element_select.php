<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Library\Php\StandardModule;
 
if (!defined('BACKEND')) exit;

class element_select extends Element{ //data element in area
  var $default_value;
  var $values;
  var $php_code_for_preview;
  function print_field_new($prefix, $parent_id = null, $area = null){
    $answer = '';
 
    
    if (isset($this->mem_value))
      $value = $this->mem_value;
    else  
      $value = $this->default_value;

    $answer .= '<select class="stdMod" name="'.$prefix.'" >';
    if (isset($this->values) != null && sizeof($this->values) > 0)
    foreach($this->values as $key => $current_value){
        if ($current_value[0] == $this->default_value || $current_value[0] == $value)
           $selected = " selected ";
        else
           $selected = ""; 
        $answer .= '<option value="'.htmlspecialchars($current_value[0]).'" '.$selected.'>'.htmlspecialchars($current_value[1]).'</option>';
    }
    $answer .= '</select>';  
    return $answer;
  }

  function memorize($prefix){
      if(isset($_REQUEST[$prefix]))
        $this->mem_value = $_REQUEST[$prefix];
  }
  
  function reset(){
    unset($this->mem_value);
  }

  function print_field_update($prefix, $parent_id = null, $area = null){
    $value = null;
    $answer = '';
   
    if ($area){
       $sql = "select t.".$this->get_db_field()." from `".DB_PREF."".$area->get_db_table()."` t where ".$area->get_db_key()." = '".$parent_id."' ";
       $rs = mysql_query($sql);
       if (!$rs)
          trigger_error("Can not get text field data. ".$sql);
       if ($lock = mysql_fetch_assoc($rs)){
         $value = $lock[''.$this->get_db_field()];
       }
    }
    
    if ($value == "")
      $value = $this->default_value;

    $answer .= '<select class="stdMod" name="'.$prefix.'" >';
    $value_exists = false;
    if (isset($this->values) != null && sizeof($this->values) > 0)
    foreach($this->values as $key => $current_value){
        if ($current_value[0] == $this->default_value || $current_value[0] == $value){
           $selected = " selected=\"\" ";
           $value_exists = true;
        }else
           $selected = ""; 
        $answer .= '<option value="'.htmlspecialchars($current_value[0]).'" '.$selected.'>'.htmlspecialchars($current_value[1]).'</option>';
    }
    if(!$value_exists){
        $answer .= '<option value="'.htmlspecialchars($value).'" selected>'.htmlspecialchars($value).' (not in list)</option>';
    }
    
    $answer .= '</select>';  
    return $answer;  
  }


  function get_parameters($action, $prefix){
    if($this->read_only)
      return;
    else
     return array("name"=>$this->get_db_field(), "value"=>$_REQUEST[''.$prefix]);
  }


  function check_field($prefix, $action){
    global $parametersMod;
    if ($this->required && (!isset($_POST[$prefix]) || $_POST[$prefix] == ''))
      return $std_par = $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');
  }



  function preview_value($value){
    eval ($this->php_code_for_preview);
    return $value;
  }

  function set_default_value($default_value){
     $this->default_value = $default_value;
  }
  function get_default_value(){     
     return $this->default_value;
  }
  function set_values($values){
    $this->values = $values;
  }


  function set_php_code_for_preview($php_code_for_preview){
    $this->php_code_for_preview = $php_code_for_preview;
  }
}

