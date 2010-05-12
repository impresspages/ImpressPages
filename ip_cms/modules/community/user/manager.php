<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see licence.html
 */

namespace Modules\community\user;


if (!defined('BACKEND')) exit;
  
require_once(LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once(__DIR__."/db.php");
require_once(__DIR__."/config.php");

class mod_community_user_area extends \Library\Php\StandardModule\Area{
  function after_insert($id){  
    $db_mod = new Db();
    $db_mod->loginTimestamp($id);
  }
}


class mod_community_user_gallery_area extends \Library\Php\StandardModule\Area{
  function after_insert($id){
    $sql = "";
  }
}


class Manager{
   var $standard_module;   
   function __construct(){
     global $parametersMod;
     
     global $db; 
     
/* user */

     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('community', 'user', 'admin_translations', 'login');
     $element->db_field = "login";
     $element->show_on_list = true;
     $element->searchable = true;
     $element->required = true;
     $element->sortable = true;
     $elements[] = $element;

 
     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('community', 'user', 'admin_translations', 'email');
     $element->db_field = "email";
     $element->show_on_list = true;
     $element->reg_expression = $parametersMod->getValue('developer', 'std_mod','parameters','email_reg_expression');
     $element->reg_expression_for_user = $parametersMod->getValue('community', 'user','admin_translations','error_email');
     $element->searchable = true;
     $element->sortable = true;
     $element->required = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_pass();
     $element->name = $parametersMod->getValue('community', 'user', 'admin_translations', 'password');
     $element->db_field = "password";
     $element->use_hash = $parametersMod->getValue('community', 'user', 'options', 'use_password_hash');
     $element->hash_salt = Config::$hashSalt;
     if(!$parametersMod->getValue('community', 'user', 'options', 'use_password_hash'))
       $element->show_on_list = true;
     $elements[] = $element;


 
     $element = new \Library\Php\StandardModule\element_bool();
     $element->searchable = true;
     $element->name = $parametersMod->getValue('community', 'user', 'admin_translations', 'verified');
     $element->db_field = "verified";
     $element->show_on_list = true;
     $element->sortable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('community', 'user', 'admin_translations', 'created_on');
     $element->db_field = "created_on";
     $element->show_on_list = false;
     $element->read_only = true;
     $element->searchable = true;
     $element->sortable = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('community', 'user', 'admin_translations', 'warned_on');
     $element->db_field = "warned_on";
     $element->show_on_list = false;
     $element->read_only = true;
     $element->searchable = true;
     $element->sortable = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('community', 'user', 'admin_translations', 'last_login');
     $element->db_field = "last_login";
     $element->show_on_list = false;
     $element->read_only = true;
     $element->searchable = true;
     $element->sortable = true;
     $elements[] = $element;


     $area0 = new mod_community_user_area();
     $area0->db_table = "m_community_user";
     $area0->name = $parametersMod->getValue('community', 'user', 'admin_translations', 'user');
     $area0->db_key = "id";
     $area0->elements = $elements; 
		 $area0->sort_field = 'row_number';
		 $area0->sortable = false;
		 $area0->searchable = true;
		 $area0->order_by = "id";


     
     $this->standard_module = new \Library\Php\StandardModule\StandardModule($area0);
   }
   function manage(){
    return $this->standard_module->manage();
     
   }
  

}
