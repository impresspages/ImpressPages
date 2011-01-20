<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\email_queue; 

if (!defined('BACKEND')) exit; 
require_once (BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once (__DIR__.'/element_email.php');
require_once (__DIR__.'/element_attachment.php');


class Manager{
   var $standard_module;   
   function __construct(){
     global $parametersMod;
     
     


     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'to');
     $element->db_field = "to";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $element->sortable = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'from');
     $element->db_field = "from";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $element->sortable = true;
     $elements[] = $element;

		 
     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'subject');
     $element->db_field = "subject";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $element->sortable = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_bool();
     $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'immediate');
     $element->db_field = "immediate";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $element->sortable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'send');
     $element->db_field = "send";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $element->sortable = true;
     $elements[] = $element;


     /*$element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'email');
     $element->db_field = "email";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;*/


     $element = new element_email();
     $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'email');
     $element->db_field = "id";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;
		 
     $element = new element_attachment();
     $element->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'attachments');
     $element->db_field = "id";
     $element->show_on_list = true;
     $element->read_only = true;
     $element->searchable = true;
     $elements[] = $element;
		 
		 
     $area0 = new \Library\Php\StandardModule\Area();
     $area0->db_table = "m_administrator_email_queue";
     $area0->name = $parametersMod->getValue('administrator', 'email_queue', 'admin_translations', 'email_queue');
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
