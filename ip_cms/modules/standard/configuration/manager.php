<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\standard\configuration;

if (!defined('BACKEND')) exit;  

require_once(BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');


class Manager{
   var $standardModule;   
   function __construct(){
     global $parametersMod;
     

     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
		 $tmp_element = $element;
     $elements[] = $element;




/*     $element = new element_text("text");
     $element->name = $mod_parameters['translations']['key']->value;
     $element->db_field = "name";
     $element->show_on_list = true;
  //   $element->searchable = true;
     $elements[] = $element;*/

     $area0 = new \Library\Php\StandardModule\Area();
     $area0->db_table = "module_group";
     $area0->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','module_groups');
     $area0->db_key = "id";
     $area0->elements = $elements; 
     $area0->sort_field = "row_number";
     $area0->sortable = false;
     $area0->where_condition = " admin=0 ";
     $area0->permission = 'read_only';
		 $area0->order_by = 'row_number';
		 $area0->name_element = $tmp_element;











     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
		 $tmp_element = $element;
     $elements[] = $element;


    /* $element = new element_text("text");
     $element->name = $mod_parameters['translations']['key']->value;
     $element->db_field = "name";
     $element->show_on_list = true;
  //   $element->searchable = true;
     $elements[] = $element;*/





     $area1 = new \Library\Php\StandardModule\Area();
     $area1->db_table = "module";
     $area1->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','modules');
     $area1->db_key = "id";
     $area1->elements = $elements; 
     $area1->sort_field = "row_number";
     $area1->where_condition = " admin=0 ";
     $area1->db_reference = "group_id";     
     $area1->permission = 'read_only';
		 $area1->order_by = 'row_number';
		 $area1->name_element = $tmp_element;
     








//==============================================





     $elements = array();    





     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;



     $area2 = new \Library\Php\StandardModule\Area();
     $area2->db_table = "parameter_group";
     $area2->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','parameter_groups');
     $area2->db_key = "id";
     $area2->where_condition = " admin=0 ";
     $area2->elements = $elements; 
     $area2->sort_field = "row_number";
     $area2->db_reference = "module_id"; 
		 $area2->order_by = 'row_number';
     $area2->permission = 'read_only';




//==============================================



     $elements = array();    
     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;





     $element = new \Library\Php\StandardModule\element_parameter();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','value');
     $element->db_field = "id";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;



/*     $element = new element_text("comment");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','comment');
     $element->db_field = "comment";
     $element->show_on_list = true;
     $element->read_only = true;
	
     $elements[] = $element;*/
		 
		 
		 

     $area3 = new \Library\Php\StandardModule\Area();
     $area3->db_table = "parameter";
     $area3->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','parameters');
     $area3->db_key = "id";
     $area3->elements = $elements;
     $area3->where_condition = " admin=0 ";      
     $area3->sort_field = "row_number";
     $area3->db_reference = "group_id";
		 $area3->order_by = 'translation';
     $area3->permission = 'update_only';
     
          

     $area2->area = $area3;
     $area1->area = $area2;
     $area0->area = $area1;

  
     $this->standardModule = new \Library\Php\StandardModule\StandardModule($area0, 2);
     
   }
   function manage(){
    return $this->standardModule->manage();
     
   }
  

}
