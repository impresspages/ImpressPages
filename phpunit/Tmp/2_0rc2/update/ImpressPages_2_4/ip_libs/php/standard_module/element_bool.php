<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Library\Php\StandardModule;

if (!defined('BACKEND')) exit;

class element_bool extends Element{ //data element in area
    var $default_value;
    var $pre_value;
    function print_field_new($prefix, $parent_id = null, $area = null){
        $html = new std_mod_html_output();
        if(isset($this->pre_value)){
            $html->input_checkbox($prefix, $this->pre_value, $this->read_only);
        }else
        $html->input_checkbox($prefix, $this->default_value, $this->read_only);
        return $html->html;
    }


    function memorize($prefix){
        if(isset($_REQUEST[$prefix]))
        $this->pre_value = 1;
        else
        $this->pre_value = 0;
    }

    function reset(){
        unset($this->pre_value);
    }


    function print_field_update($prefix, $parent_id = null, $area = null){
        $value = null;

        if ($area){
            $sql = "select t.".$this->get_db_field()." from `".DB_PREF."".$area->get_db_table()."` t where ".$area->get_db_key()." = '".$parent_id."' ";
            $rs = mysql_query($sql);
            if (!$rs)
            trigger_error("Can not get bool field data. ".$sql);
            if ($lock = mysql_fetch_assoc($rs)){
                $value = $lock[''.$this->get_db_field()];
            }
        }
        $html = new std_mod_html_output();
        if(isset($this->pre_value))
        $value = $this->pre_value;
        if ($value)
        $html->input_checkbox($prefix, true, $this->read_only);
        else
        $html->input_checkbox($prefix, false, $this->read_only);
        return $html->html;
    }

    function get_parameters($action, $prefix){
        if($this->read_only)
        return;

        if (isset($_REQUEST[''.$prefix]))
        $value = 1;
        else
        $value = 0;
        return array("name"=>$this->get_db_field(), "value"=>$value);
    }


    function print_search_field($level, $key){
        global $parametersMod;
        $checked1 = '';
        $checked2 = '';
        if (isset($_GET['search'][$level][$key])){
            if($_GET['search'][$level][$key] == 1){
                $checked1 = " checked ";
            }else{
                $checked2 = " checked ";
            }
        }

        return '<span class="label"><input class="stdModRadio" type="radio" '.$checked1.' name="search['.$level.']['.$key.']" value="1" />'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'yes').'</span>'.
		'<span class="label"><input  class="stdModRadio" type="radio" '.$checked2.' name="search['.$level.']['.$key.']" value="0" />'.$parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'no').'</span>';
    }

    function get_filter_option($value){
        if($value)
        return " ".$this->db_field." = 1 ";
        else
        return " ".$this->db_field." = 0 ";
    }



    function preview_value($value){
        if ($value == 1)
        return "+";
        else
        return "-";
    }

    function check_field($prefix, $action){
        return null;
    }




}

?>
