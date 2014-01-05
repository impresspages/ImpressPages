<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Email;
//TODOXX refactor #132
class element_email extends \Library\Php\StandardModule\Element{ //data element in area
    var $default_value;
    var $mem_value;
    var $reg_expression;
    var $max_length;

    function __construct(){
        $this->visible = false;
    }

    function print_field_new($prefix, $parentId = null, $area = null){

        return '';
    }


    function print_field_update($prefix, $parentId = null, $area = null){
        return '';
    }

    function get_parameters($action, $prefix){
        return;
    }

    function print_search_field($level, $key){
        if (isset($_GET['search'][$level][$key]))
        $value = $_GET['search'][$level][$key];
        else
        $value = '';
        return '<input name="search['.$level.']['.$key.']" value="'.htmlspecialchars($value).'" />';
    }

    function get_filter_option($value){
        return " email like '%".ip_deprecated_mysql_real_escape_string($value)."%' ";
    }

    function preview_value($value){
        global $cms;
        return '<span style="cursor:pointer;" onclick="window.open(\''.$cms->generateWorkerUrl($cms->curModId, 'action=preview&record_id='.((int)$value)).'\',\'mywindow\',\'width=700,height=800,resizable=yes,scrollbars=yes,location=no,directories=no,menubar=no,copyhistory=no\')">'.__('Preview', 'ipAdmin').'</span>';
    }

    function check_field($prefix, $action){
        return null;
    }



}

