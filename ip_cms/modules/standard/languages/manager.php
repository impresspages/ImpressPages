<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\languages;
if (!defined('BACKEND')) exit;
  
require_once (BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once (__DIR__.'/db.php');


class LanguagesArea extends \Library\Php\StandardModule\Area{
	var $errors = array();

  function after_insert($id){
    Db::createRootZoneElement($id);
    Db::createEmptyTranslations($id,'par_lang');
  }
  
  function after_delete($id){
    Db::deleteRootZoneElement($id);
    Db::deleteTranslations($id, 'par_lang');
  }  
  
  function allow_delete($id){
    global $parametersMod;
    require_once (MODULE_DIR."standard/content_management/db.php");

    $dbContentManagement = new \Modules\standard\content_management\Db();

    $answer = true;

		
    $zones = Db::getZones();
    foreach($zones as $key => $zone){
      $rootElement = $dbContentManagement->rootMenuElement($zone['id'], $id);  
      $elements = $dbContentManagement->menuElementChildren($rootElement);
      if(sizeof($elements) > 0){
        $answer = false;
				$this->errors['delete'] = $parametersMod->getValue('standard', 'languages', 'admin_translations', 'cant_delete_not_empty_language');
			}
    }
    
		if(sizeof(Db::getLanguages()) ==1){
			$answer = false;
			$this->errors['delete'] = $parametersMod->getValue('standard', 'languages', 'admin_translations', 'cant_delete_last_language');
		}
			

		return $answer;
  }

	function last_error($action){
		if(isset($this->errors[$action]))
			return $this->errors[$action];
		else
			return '';
	}
	
  function allow_delete_error($id){
    global $parametersMod;
    return $parametersMod->getValue('standard', 'languages', 'admin_translations', 'cant_delete');
  }

}


class Manager{
   var $standardModule;   
   function __construct(){
     global $parametersMod;
     
     


     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('standard','languages','admin_translations','short');
     $element->db_field = "d_short";
     $element->show_on_list = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('standard','languages','admin_translations','long');
     $element->db_field = "d_long";
     $element->show_on_list = true;
     $elements[] = $element;


 
     $element = new \Library\Php\StandardModule\element_bool();
     $element->name = $parametersMod->getValue('standard','languages','admin_translations','visible');
     $element->db_field = "visible";
     $element->show_on_list = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_text("url");
     $element->name = $parametersMod->getValue('standard','languages','admin_translations','url');
     $element->db_field = "url";
     $element->show_on_list = true;
     $element->required = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text("code");
     $element->name = $parametersMod->getValue('standard','languages','admin_translations','code');
     $element->db_field = "code";
     $element->show_on_list = true;
		 $element->required = true;
     $elements[] = $element;

     $area0 = new LanguagesArea();
     $area0->db_table = "language";
     $area0->name = $parametersMod->getValue('standard','languages','admin_translations','languages');
     $area0->db_key = "id";
     $area0->elements = $elements; 
		 $area0->sort_field = 'row_number';
		 $area0->sortable = true;
		 $area0->new_record_position= "bottom";		 
		 $area0->order_by = "row_number";


     
     $this->standardModule = new \Library\Php\StandardModule\StandardModule($area0);
   }
   function manage(){
    return $this->standardModule->manage();
     
   }
  

}
