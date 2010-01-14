<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Library\Php\StandardModule;
 
if (!defined('BACKEND')) exit;

class element_hidden extends Element{ //data element in area
  var $default_value;
	function __construct(){
		$this->visible =false;
	}
	
  function print_field_new($prefix, $parent_id = null, $area = null){
    $value = null;
 
    if ($area){
       $sql = "select t.".$this->get_db_field()." from ".DB_PREF."".$area->get_db_table()." t where ".$area->get_db_key()." = '".$parent_id."' ";
       $rs = mysql_query($sql);
       if (!$rs)
          trigger_error("Can not get text field data. ".$sql);
       if ($lock = mysql_fetch_assoc($rs)){
         $value = $lock[''.$this->get_db_field()];
       }
    }
    if ($value == "")
       $answer =  '<input type="hidden" name="'.$prefix.'" value="'.$this->default_value.'"/><br>';
    else 
       $answer = '<input type="hidden" name="'.$prefix.'" value="'.$value.'"/><br>';
    return $answer;         
         
  }



  function print_field_update($prefix, $parent_id = null, $area = null){
    $value = null;
 
    if ($area){
       $sql = "select t.".$this->get_db_field()." from ".DB_PREF."".$area->get_db_table()." t where ".$area->get_db_key()." = '".$parent_id."' ";
       $rs = mysql_query($sql);
       if (!$rs)
          trigger_error("Can not get text field data. ".$sql);
       if ($lock = mysql_fetch_assoc($rs)){
         $value = $lock[''.$this->get_db_field()];
       }
    }
    if ($value == "")
       $answer =  '<input type="hidden" name="'.$prefix.'" value="'.$this->default_value.'"/><br>';
    else 
       $answer = '<input type="hidden" name="'.$prefix.'" value="'.$value.'"/><br>';
    return $answer;         
  }


  function get_parameters($action, $prefix){
    if($this->read_only)
      return;
    else
  
     return array("name"=>$this->get_db_field(), "value"=>$_REQUEST[''.$prefix]);
  }

  function preview_value($value){
    return $value;
  }



  function set_default_value($default_value){
     $this->default_value = $default_value;
  }
  function get_default_value(){
     return $this->default_value;
  }


}

