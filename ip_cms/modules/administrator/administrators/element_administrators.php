<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Modules\administrator\administrators;  
if (!defined('BACKEND')) exit;

class element_administrators extends \Library\Php\StandardModule\Element{ //data element in area
  var $default_value;
  var $values;
  var $php_code_for_preview;
  function print_field_new($prefix, $parent_id = null, $area = null){
    $answer = '';
 
    
    if (isset($this->mem_value))
      $value = $this->mem_value;
    else  
      $value = $this->default_value;

    $answer .= '';
    if (isset($this->values) != null && sizeof($this->values) > 0)
    foreach($this->values as $key => $group){
      $answer .= '<br/><span class="label"><b>&nbsp;&nbsp;&nbsp;'.htmlspecialchars($group['title']).'</b></span><br/>';
      foreach($group['values'] as $key_value => $current_value){
        if ($current_value[0] == $this->default_value || $current_value[0] == $value)
           $selected = ' checked="yes" ';
        else
           $selected = ""; 
        $answer .= '&nbsp;&nbsp;&nbsp;<input class="stdModBox" name="'.$prefix.'['.$current_value[0].']" type="checkbox" '.$selected.' />'.htmlspecialchars($current_value[1]).'<br />';
      }
      $answer .= '';
    }
    $answer .= '';
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
    $values = array();
    $answer = '';
   
    if ($area){
       $sql = "select * from `".DB_PREF."user_to_mod` utm where user_id = '".$parent_id."' ";
       $rs = mysql_query($sql);
       if (!$rs)
          trigger_error("Can not get text field data. ".$sql);
       while ($lock = mysql_fetch_assoc($rs)){
         $values[$lock['module_id']] = 1;
       }
    }
    

    $value_exists = false;
    if (isset($this->values) != null && sizeof($this->values) > 0)
    foreach($this->values as $key => $group){
      $answer .= '<br/><span class="label"><b>&nbsp;&nbsp;&nbsp;'.htmlspecialchars($group['title']).'</b></span><br/>';
      foreach($group['values'] as $key_value => $current_value){
        if (isset($values[$current_value[0]])){
           $selected = ' checked="yes" ';
           $value_exists = true;
        }else
           $selected = ""; 
        $answer .= '&nbsp;&nbsp;&nbsp;<input class="stdModBox" name="'.$prefix.'['.$current_value[0].']" type="checkbox" '.$selected.' />'.htmlspecialchars($current_value[1]).'<br />';
      }
    }
    
    return $answer;  
  }


  function process_insert( $prefix, $area,$id){
    
    foreach($_REQUEST[$prefix] as $key => $value){
      $sql = "insert into `".DB_PREF."user_to_mod` set user_id = ".(int)$id.", module_id = ".(int)$key." ";
      $rs = mysql_query($sql);
      if(!$rs)
        trigger_error($sql." ".mysql_error());
    }

  

  }


  function process_update( $prefix, $area,$id){
    $sql = "delete from `".DB_PREF."user_to_mod` where user_id = ".(int)$id."";
    $rs = mysql_query($sql);
    if(!$rs)
      trigger_error($sql);
      
    foreach($_REQUEST[$prefix] as $key => $value){
      $sql = "insert into `".DB_PREF."user_to_mod` set user_id = ".(int)$id.", module_id = ".(int)$key." ";
      $rs = mysql_query($sql);
      if(!$rs)
        trigger_error($sql." ".mysql_error());
    }

  

  }

  function get_parameters($action, $prefix){
    return;
  }


  function check_field($prefix, $action){
    return null;
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

