<?php
/**
 * @package		Library
 *
 *
 */
namespace Library\Php\StandardModule;


class element_text extends Element{ //data element in area
    var $default_value;
    var $mem_value;
    var $reg_expression;
    var $max_length;
    function print_field_new($prefix, $parent_id = null, $area = null){
        $html = new std_mod_html_output();
        $value = null;
        if(isset($this->mem_value)){
            if($this->max_length)
            $html->input($prefix, $this->mem_value, $this->read_only, $this->max_length);
            else
            $html->input($prefix, $this->mem_value, $this->read_only);
        }else{
            if($this->max_length)
            $html->input($prefix, $this->default_value, $this->read_only, $this->max_length);
            else
            $html->input($prefix, $this->default_value, $this->read_only);
        }
        return $html->html;

    }

    function memorize($prefix){
        if(isset($_POST[$prefix]))
        $this->mem_value = $_POST[$prefix];
    }

    function reset(){
        unset($this->mem_value);
    }


    function print_field_update($prefix, $parent_id = null, $area = null){
        $value = null;

        if ($area){
            $sql = "select t.".$this->get_db_field()." from `".DB_PREF."".$area->get_db_table()."` t where ".$area->get_db_key()." = '".$parent_id."' ";
            $rs = mysql_query($sql);
            if (!$rs)
            trigger_error("Can not get text field data. ".$sql);
            if ($lock = mysql_fetch_assoc($rs)){
                $value = $lock[''.$this->get_db_field()];
            }
        }

        $html = new std_mod_html_output();
        $answer = '';
        if(isset($_POST[$prefix])){
            if($this->max_length)
            $html->input($prefix, $_POST[$prefix], $this->read_only, $this->max_length);
            else
            $html->input($prefix, $_POST[$prefix], $this->read_only);
        }else{
            if($this->max_length)
            $html->input($prefix, $value, $this->read_only, $this->max_length);
            else
            $html->input($prefix, $value, $this->read_only);
        }
        return $html->html;



    }

    function get_parameters($action, $prefix){
        if($this->read_only)
        return;
        else
        return array("name"=>$this->get_db_field(), "value"=>$_REQUEST[''.$prefix]);
    }


    function preview_value($value){
        return htmlspecialchars($value);
    }

    function check_field($prefix, $action){
        global $parametersMod;
        if ($this->required && (!isset($_POST[$prefix]) || $_POST[$prefix] == null))
        return $std_par = $parametersMod->getValue('developer', 'std_mod','admin_translations','error_required');

        if($this->reg_expression != null){
            if($_POST[$prefix] == null || preg_match($this->reg_expression, $_POST[$prefix]))
            return null;
            else
            return $this->reg_expression_for_user.'&nbsp;';
        }
        return null;
    }




    function print_search_field($level, $key){
        if (isset($_GET['search'][$level][$key]))
        $value = $_GET['search'][$level][$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function get_filter_option($value){
        return " `".$this->db_field."` like '%".mysql_real_escape_string($value)."%' ";
    }

    function set_default_value($default_value){
        $this->default_value = $default_value;
    }
    function get_default_value(){
        return $this->default_value;
    }


}

