<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Library\Php\StandardModule;
 
if (!defined('BACKEND')) exit;

class element_pass extends Element{ //data element in area
  var $mem_value;
  var $mem_value2;
  var $use_hash = true;
  var $hash_salt = '';
  function print_field_new($prefix, $parent_id = null, $area = null){
    
    $html = new std_mod_html_output();
    if(isset($this->mem_value))    
      $html->input_password($prefix, $this->mem_value, $_REQUEST[$prefix.'_confirm'], $this->read_only);
    else  
      $html->input_password($prefix, $this->default_value, $this->default_value, $this->read_only);
    return $html->html;


         
  }

  function memorize($prefix){
      if(isset($_REQUEST[$prefix]))
        $this->mem_value = $_REQUEST[$prefix];
      if(isset($_REQUEST[$prefix.'_confirm']))
        $this->mem_value2 = $_REQUEST[$prefix.'_confirm'];
  }
  
  function reset(){
    unset($this->mem_value);
    unset($this->mem_value2);
  }

  function print_field_update($prefix, $parent_id = null, $area = null){


    $html = new std_mod_html_output();
    if(isset($_REQUEST[$prefix])&&isset($_REQUEST[$prefix.'_confirm']))    
      $html->input_password($prefix, $_REQUEST[$prefix], $_REQUEST[$prefix.'_confirm'], $this->read_only);
    else
      $html->input_password($prefix, $this->default_value, $this->default_value, $this->read_only);
    return $html->html;

         
  }


  function get_parameters($action, $prefix){
    if($this->read_only)
      return;
  
     if (isset($_REQUEST[''.$prefix]) && $_REQUEST[''.$prefix] != ""){
        if($this->use_hash){
          $tmp_password = md5($_REQUEST[''.$prefix].$this->hash_salt);
        }else $tmp_password = $_REQUEST[''.$prefix];
       return array("name"=>$this->get_db_field(), "value"=>$tmp_password);
     }else
       return false;
  }


  function preview_value($value){
    return $value;
  }

  function check_field($prefix, $action){
    if ($_REQUEST[''.$prefix] != $_REQUEST[''.$prefix.'_confirm'] )
       return "Passwords dont match";
    else
       return null;
  }
 


}

