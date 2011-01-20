<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\developer\modules_configuration;

if (!defined('BACKEND')) exit;
  
require_once(BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once(__DIR__.'/db.php');

class ConfigurationArea extends \Library\Php\StandardModule\Area{
  function after_delete($id){
    global $db;
    Db::deletePermissions($id);
  }
  function after_insert($id){
    global $cms;
    Db::addPermissions($id, $cms->session->userId());
  }
}

class Manager{
   var $standardModule;   
   function __construct(){
     global $parametersMod;
     
     global $db; 


     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;
		$tmp_el = $element;



     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
     $element->db_field = "name";
     $element->reg_expression = "/^[A-Za-z0-9\-_]+$/";     
     $element->reg_expression_for_user = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','error_incorrect_name');
     
     $element->show_on_list = true;
  //   $element->searchable = true;
     $elements[] = $element;
     
     $element = new \Library\Php\StandardModule\element_bool();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
     $element->db_field = "admin";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;     
     

		 
     $area0 = new \Library\Php\StandardModule\Area();
     $area0->db_table = "module_group";
     $area0->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','module_groups');
     $area0->db_key = "id";
     $area0->elements = $elements; 
     $area0->sort_field = "row_number";
     $area0->sortable = true;
		 $area0->order_by = 'row_number';
		 $area0->name_element = $tmp_el;











     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;
		$tmp_el = $element;

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
     $element->db_field = "name";
     $element->reg_expression = "/^[A-Za-z0-9\-_]+$/";     
     $element->reg_expression_for_user = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','error_incorrect_name');
     
     $element->show_on_list = true;
  //   $element->searchable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_bool();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
     $element->db_field = "admin";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;




     $area1 = new ConfigurationArea();
     $area1->db_table = "module";
     $area1->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','modules');
     $area1->db_key = "id";
     $area1->elements = $elements; 
     $area1->sort_field = "row_number";
     $area1->db_reference = "group_id";     
     $area1->sortable = true;
		 $area1->order_by = 'row_number';
		 $area1->name_element = $tmp_el;
     








//==============================================





     $elements = array();    



		 
     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;
		$tmp_el = $element;		 



     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
     $element->db_field = "name";
     $element->reg_expression = "/^[A-Za-z0-9\-_]+$/";     
     $element->reg_expression_for_user = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','error_incorrect_name');
     
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_bool();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
     $element->db_field = "admin";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;



     $area2 = new \Library\Php\StandardModule\Area();
     $area2->db_table = "parameter_group";
     $area2->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','parameter_groups');
     $area2->db_key = "id";
     $area2->elements = $elements; 
     $area2->sort_field = "row_number";
     $area2->db_reference = "module_id"; 
		 $area2->order_by = 'translation';
		 $area2->name_element = $tmp_el;



//==============================================



     $elements = array();    
     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;
		$tmp_el = $element;		 



     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
     $element->db_field = "name";
     $element->reg_expression = "/^[A-Za-z0-9\-_]+$/";     
     $element->reg_expression_for_user = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','error_incorrect_name');
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;


 

     $element = new \Library\Php\StandardModule\element_parameter();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','value');
     $element->db_field = "id";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;

/*
     $element = new element_text();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','translations','regexpression');
     $element->db_field = "regexpression";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;*/


/*     $element = new element_text("comment");
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','translations','comment');
     $element->db_field = "comment";
     $element->show_on_list = true;
  //   $element->searchable = true;
     $elements[] = $element;*/
		 
		 
		 
     $element = new \Library\Php\StandardModule\element_bool();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
     $element->db_field = "admin";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;

		 
		 
     $area3 = new \Library\Php\StandardModule\Area();
     $area3->db_table = "parameter";
     $area3->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','parameters');
     $area3->db_key = "id";
     $area3->elements = $elements; 
     $area3->sort_field = "row_number";
     $area3->db_reference = "group_id";     
		 $area3->order_by = 'translation';
		 $area3->name_element = $tmp_el;
		 
		 
     $area2->set_area($area3);
     $area1->set_area($area2);
     $area0->set_area($area1);


     $this->standardModule = new \Library\Php\StandardModule\StandardModule($area0, 2);
     
   }
   function manage(){
    return $this->standardModule->manage();
     
   }
  

}
