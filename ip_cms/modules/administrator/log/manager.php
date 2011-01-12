<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */


namespace Modules\administrator\log;


if (!defined('BACKEND')) exit; 
require_once (BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');


class Manager{
   var $standard_module;   
   function __construct(){
     global $parametersMod;
     
     


     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'module');
     $element->db_field = "module";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'time');
     $element->db_field = "time";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'name');
     $element->db_field = "name";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'value_str');
     $element->db_field = "value_str";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'value_int');
     $element->db_field = "value_int";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'log', 'admin_translations', 'value_float');
     $element->db_field = "value_float";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;

     $area0 = new \Library\Php\StandardModule\Area();
     $area0->db_table = "log";
     $area0->name = "Log";
     $area0->db_key = "id";
     $area0->elements = $elements; 
     $area0->searchable = true;
		 $area0->permission = 'read_only';
			$area0->order_by = 'id';
			$area0->order_by_dir = "desc";
     $area0->rows_per_page = 100;


     $this->standard_module = new \Library\Php\StandardModule\StandardModule($area0);
   }
   function manage(){
    return $this->standard_module->manage();
     
   }
  

}
