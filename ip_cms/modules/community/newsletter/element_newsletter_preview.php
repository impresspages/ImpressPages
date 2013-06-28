<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\community\newsletter;

if (!defined('BACKEND')) exit;
class element_newsletter_preview extends \Library\Php\StandardModule\Element{ //data element in area
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


    function preview_value($value){
        global $parametersMod;
        global $cms;
        return '<span style="cursor:pointer;" onclick="window.open(\''.$cms->generateWorkerUrl($cms->curModId, 'action=preview&record_id='.$value).'\',\'mywindow\',\'width=700,height=800,resizable=yes,scrollbars=yes,location=no,directories=no,menubar=no,copyhistory=no\')">'.htmlspecialchars($parametersMod->getValue('community','newsletter','admin_translations','preview')).'</span>';
    }

    function check_field($prefix, $action){
        return null;
    }



}

