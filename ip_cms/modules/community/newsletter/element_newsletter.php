<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter;

if (!defined('BACKEND')) exit;  
class element_newsletter extends \Library\Php\StandardModule\Element{ //data element in area
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
    return '<span style="cursor:pointer;" onclick="if(confirm(\''.htmlspecialchars($parametersMod->getValue('community','newsletter','admin_translations','send_or_not_question')).'\')) LibDefault.ajaxMessage(\''.$cms->generateWorkerUrl($cms->curModId, 'record_id='.$value.'&action=send').'\', \'\');">'.htmlspecialchars($parametersMod->getValue('community','newsletter','admin_translations','send')).'</span>';
  }

  function check_field($prefix, $action){    
    return null;
  }



}

