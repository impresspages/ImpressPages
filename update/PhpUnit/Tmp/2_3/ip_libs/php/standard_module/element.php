<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Library\Php\StandardModule;

if (!defined('BACKEND')) exit;

class Element{ //data element in area
    var $db_field;
    var $name;
    var $required;
    var $searchable;
    var $show_on_list;
    var $attributes;
    var $read_only;
    var $default_value;
    var $sortable;
    var $visible;

    function __construct(){
        $this->read_only = false;
        $this->sortable = false;
        $this->visible = true;
    }


    function print_field($key, $reference = null, $area = null){
        $value = null;
        switch($this->get_type()){
            case 'text':
                if ($area){
                    $sql = "select t.".$this->get_db_field()." from `".DB_PREF."".$area->get_db_table()."` t where ".$area->get_db_key()." = '".$area->get_db_reference()."' ";
                    $rs = mysql_query($sql);
                    if (!$rs)
                    trigger_error("Can not get text field data. ".$sql);
                    if ($lock = mysql_fetch_assoc($rs))
                    $value = $lock[''.$this->get_db_field()];
                }
                echo '<input type="text" name="i_'.$key.'" value="'.$value.'"/><br>';
                break;


        }
    }


    function get_parameters($action, $prefix){

    }


    function check_field($key, $action){

    }


    function process_insert($prefix, $area, $last_insert_id){

    }
    function process_update($prefix, $area, $row_id){

    }
    function process_delete($area, $key){

    }

    function preview_value($value){
    }

    function print_search_field($level, $key){

    }

    function get_type(){
        return $this->type;
    }
    function set_type($type){
        $this->type = $type;
    }
    function get_name(){
        return $this->name;
    }
    function set_name($name){
        $this->name = $name;
    }
    function get_db_field(){
        return $this->db_field;
    }
    function set_db_field($db_field){
        $this->db_field = $db_field;
    }
    function get_required(){
        return $this->required;
    }
    function set_required($required){
        $this->required = $required;
    }
    function get_searchable(){
        return $this->searchable;
    }
    function set_searchable($searchable){
        $this->searchable = $searchable;
    }
    function get_show_on_list(){
        return $this->show_on_list;
    }
    function set_show_on_list($show_on_list){
        $this->show_on_list = $show_on_list;
    }
    function get_attributes(){
        return $this->attributes;
    }
    function set_attributes($attributes){
        $this->attributes = $attributes;
    }

    function set_translation_options($options){
        $this->translation_options = $options;
    }

    /*executed on all fields if one or some fileds contains errors.
     memorizes currently posted data
     Can bee used to store uploaded file to tmp foler etc.
     */
    function memorize($prefix){

    }
    /*deletes memorized data
     resets other data. Called when changing current area, after insert or update*/
    function reset(){
    }


}

