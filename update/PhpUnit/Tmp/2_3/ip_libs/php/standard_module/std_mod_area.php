<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Library\Php\StandardModule;

if (!defined('BACKEND')) exit;



class Area{ //data structure. Represents any data module
    var $db_table; //db table name
    var $db_key;  //key of current table
    var $db_reference; //reference to some other table
    var $parent_id; //reference value
    var $name; //
    var $elements;  //data elements
    var $where_condition;  //extra condition to sql where part
    var $rows_per_page;
    var $permission;
    var $searchable;
    var $visible; //set to false if you wish to hide last level of tree. Might be used when you need to delete related elements on parent deletion.

    /*sort - ability to set order values in admin to specified field. */
    var $sortable;
    var $sort_type;  //numbers, pointers
    var $sort_field; //field, where user can change the order of items on the site
    var $new_record_position; //available values: null, top, bottom. Specifies the position where new records should be placed upound other records.

    /*ordering - order of records set by default in admin area*/
    var $order_by; //default order field
    var $order_by_dir; //default order direction

    var $after_insert;
    var $current_page;

    var $name_element; //main element by whish records can be named in admin area. For example on left elements tree.

    var $area;  //next level of data tree

    function __construct(){
        $this->rows_per_page = 40;
        $this->current_page = 0;
        $this->sort_type = 'pointers';
        $this->order_by_dir = 'asc';
        $this->new_record_position = "bottom";
        $this->visible = true;
    }


    function set_rows_per_page($rows_per_page){
        $this->rows_per_page = $rows_per_page;
    }
    function get_rows_per_page(){
        return $this->rows_per_page;
    }

    function get_where_condition(){
        return $this->where_condition;
    }
    function set_where_condition($where_condition){
        $this->where_condition = $where_condition;
    }

    function get_sort_field(){
        return $this->sort_field;
    }
    function set_sort_field($sort_field){
        $this->sort_field = $sort_field;
    }

    function get_db_table(){
        return $this->db_table;
    }
    function set_db_table($db_table){
        $this->db_table = $db_table;
    }
    function get_name(){
        return $this->name;
    }
    function set_name($name){
        $this->name = $name;
    }
    function get_parent_id(){
        return $this->parent_id;
    }
    function set_parent_id($parent_id){
        $this->parent_id = $parent_id;
    }

    function get_db_key(){
        return $this->db_key;
    }
    function set_db_key($db_key){
        $this->db_key = $db_key;
    }
    function get_db_reference(){
        return $this->db_reference;
    }
    function set_db_reference($db_reference){
        $this->db_reference = $db_reference;
    }

    function &get_elements(){
        return $this->elements;
    }
    function set_elements($elements){
        $this->elements = $elements;
    }
    function &get_area(){
        return $this->area;
    }
    function set_area($area){
        $this->area = $area;
    }


}



