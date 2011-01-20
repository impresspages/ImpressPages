<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter; 

if (!defined('BACKEND')) exit;  

require_once (BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once (__DIR__.'/element_newsletter.php');
require_once (__DIR__.'/element_newsletter_preview.php');



class Manager{
   var $standard_module;   
   function __construct(){
     global $parametersMod;
     
     


     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('standard','languages','admin_translations','short');
     $element->db_field = "d_short";
     $element->show_on_list = true;
     $elements[] = $element;
		 $tmp_element = $element;


 

     $area0 = new \Library\Php\StandardModule\Area();
     $area0->db_table = "language";
     $area0->name = $parametersMod->getValue('standard','languages','admin_translations','languages');
     $area0->db_key = "id";
     $area0->elements = $elements; 
     $area0->sort_field = "row_number";
     $area0->order_by = "row_number";
		 $area0->name_element = $tmp_element;
		 
		 
     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("subject");
     $element->name = $parametersMod->getValue('community', 'newsletter','admin_translations','subject');
     $element->db_field = "subject";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_wysiwyg("text");
     $element->name = $parametersMod->getValue('community', 'newsletter','admin_translations','text');
     $element->db_field = "text";
     $element->show_on_list = true;
  //   $element->searchable = true;
     $elements[] = $element;

     $element = new element_newsletter_preview("");
     $element->name = '';
     $element->db_field = "id";
     $element->show_on_list = true;
		 $element->sortable = false;		 
    // $element->searchable = true;
     $elements[] = $element;

		 
		 
     $element = new element_newsletter("");
     $element->name = '';
     $element->db_field = "id";
     $element->show_on_list = true;
		 $element->sortable = false;		 
    // $element->searchable = true;
     $elements[] = $element;


     $area1 = new \Library\Php\StandardModule\Area();
     $area1->db_table = "m_community_newsletter";
     $area1->name = $parametersMod->getValue('community', 'newsletter','admin_translations','newsletter'); 
     $area1->db_key = "id";
     $area1->elements = $elements; 
		 $area1->order_by = "id";
		 $area1->order_by_dir = "desc";
     $area1->sortable = true; 
		 $area1->db_reference = "language_id";

		$area0->area = $area1;



     
     $this->standard_module = new \Library\Php\StandardModule\StandardModule($area0, 1);
   }
   function manage(){
    return $this->standard_module->manage();
     
   }
  

}
