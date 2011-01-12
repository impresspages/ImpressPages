<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\administrators; 
if (!defined('BACKEND')) exit;  

require_once (BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once (__DIR__.'/element_administrators.php');

class mod_administrator_area extends \Library\Php\StandardModule\Area{
  public function after_delete($id){
    $sql = "delete from `".DB_PREF."user_to_mod` where `user_id` = ".(int)$id."";
    $rs = mysql_query($sql);
    if(!$rs)
      trigger_error($sql);
  }
}


class Manager{
   var $standard_module;   
   function __construct(){
     global $parametersMod;
     
     global $cms;


     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('administrator', 'administrators','admin_translations','name');
     $element->db_field = "name";
		 $element->required = true;
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;




     $element = new \Library\Php\StandardModule\element_pass("text");
     $element->name = $parametersMod->getValue('administrator', 'administrators','admin_translations','password');
     $element->db_field = "pass";
     $element->show_on_list = false;
  //   $element->searchable = true;
     $elements[] = $element;

     $element = new element_administrators("select");
     $element->set_name($parametersMod->getValue('administrator', 'administrators','admin_translations','permissions'));
    // $element->set_db_field("id");

 
     $modules = \Backend\Db::modules(true);
     $values = array();
     foreach($modules as $key => $group){
      $tmp_values = array();
      foreach($group as $key2 => $module){
        $value = array();
        $value[] = $module['id'];
        $value[] = $module['translation'];
        $tmp_values[] = $value;
      }
      $values[] = array("title"=>$key, "values"=>$tmp_values);
     }
     
     $element->set_values($values);
     
     $code='
        global $cms;
        
       $sql = " select translation from `".DB_PREF."module` where `id` = \'".$value."\' ";
       $rs = mysql_query($sql);
       if ($rs && $lock = mysql_fetch_assoc($rs)){
          $value = htmlspecialchars($lock[\'translation\']);
       }else
        $value = \'\';
       
       if(!$rs)
        trigger_error("Can\'t get field value translation ".$sql); 
     ';
     $element->set_php_code_for_preview($code);

     $element->set_show_on_list(false);
     
     
     $elements[] = $element;

     $area0 = new mod_administrator_area();
     $area0->db_table = "user";
     $area0->name = $parametersMod->getValue('administrator', 'administrators','admin_translations','administrators');
     $area0->db_key = "id";
     $area0->elements = $elements; 
     $area0->sort_field = "row_number";




  
 
     $this->standard_module = new \Library\Php\StandardModule\StandardModule($area0);
   }
   function manage(){
    return $this->standard_module->manage();
     
   }
  

}
