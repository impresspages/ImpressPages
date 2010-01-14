<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */


namespace Modules\developer\widgets; 

if (!defined('BACKEND')) exit;
  
require_once(BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once(__DIR__.'/db.php');
require_once(__DIR__.'/installation.php');

class Area extends \Library\Php\StandardModule\area{
  function before_delete($id){
    
    $module = Db::getModule($id);
    if($module){
      if(file_exists(BASE_DIR.MODULE_DIR."standard/content_management/widgets/".$module['group_name']."/".$module['module_name']."/uninstall/script.php")){
        require_once(BASE_DIR.MODULE_DIR."standard/content_management/widgets/".$module['group_name']."/".$module['module_name']."/uninstall/script.php");
      }
    }
  }  
}


class Manager{
   var $standard_module;   
   function __construct(){
     global $parametersMod;
     
     


     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','translation');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;




     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','key');
     $element->db_field = "name";
     $element->show_on_list = true;
  //   $element->searchable = true;
     $elements[] = $element;

     $area0 = new \Library\Php\StandardModule\Area();
     $area0->db_table = "content_module_group";
     $area0->name = $parametersMod->getValue('developer', 'widgets','admin_translations','content_module_groups');
     $area0->db_key = "id";
     $area0->elements = $elements; 
		 $area0->sortable = true;		 
     $area0->sort_field = "row_number";
		 $area0->order_by = "row_number";




     $elements = array();    

     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','translation');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','key');
     $element->db_field = "name";
     $element->show_on_list = true;
  //   $element->searchable = true;
     $elements[] = $element;





     $area1 = new \Library\Php\StandardModule\Area();
     $area1->db_table = "content_module";
     $area1->name = $parametersMod->getValue('developer', 'widgets','admin_translations','modules');
     $area1->db_key = "id";
     $area1->elements = $elements; 
     $area1->sort_field = "row_number";
		 $area1->sortable = true;
     $area1->db_reference = "group_id";     
		 $area1->order_by = "row_number";
     





//==============================================





     $elements = array();    



     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','translation');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;



     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','key');
     $element->db_field = "name";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;





     $element = new \Library\Php\StandardModule\element_bool();
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','admin');
     $element->db_field = "admin";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;


     $area2 = new \Library\Php\StandardModule\Area();
     $area2->db_table = "parameter_group";
     $area2->name = $parametersMod->getValue('developer', 'widgets','admin_translations','parameter_groups');
     $area2->db_key = "id";
     $area2->elements = $elements; 
		 $area2->order_by = "name";
     $area2->db_reference = "content_module_id"; 
     $area2->visible = false;




//==============================================










     $elements = array();    
     $element = new \Library\Php\StandardModule\element_text("text");
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','translation');
     $element->db_field = "translation";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;



     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','key');
     $element->db_field = "name";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;




     $element = new \Library\Php\StandardModule\element_parameter();
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','type');
     $element->db_field = "id";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;


     $element = new \Library\Php\StandardModule\element_text();
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','regexpression');
     $element->db_field = "regexpression";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;

     $element = new \Library\Php\StandardModule\element_bool();
     $element->name = $parametersMod->getValue('developer', 'widgets','admin_translations','admin');
     $element->db_field = "admin";
     $element->show_on_list = true;
    // $element->searchable = true;
     $elements[] = $element;


     $area3 = new \Library\Php\StandardModule\Area();
     $area3->db_table = "parameter";
     $area3->name = $parametersMod->getValue('developer', 'widgets','admin_translations','parameters');
     $area3->db_key = "id";
     $area3->elements = $elements; 
     $area3->sort_field = "row_number";
		 $area3->order_by = "name";
     $area3->db_reference = "group_id";     

     $area2->set_area($area3);
     $area1->set_area($area2);
     $area0->set_area($area1);


     $this->standard_module = new \Library\Php\StandardModule\StandardModule($area0);
   }
   function manage(){
    global $cms;
    $answer = '';
    if(isset($_REQUEST['type']) == 'ajax' && $_REQUEST['action'] == 'install'){
      if($_REQUEST['action'] == 'install'){
        $errors = ModulesInstallation::getErrors($_REQUEST['module_group'], $_REQUEST['module']); 
        if($errors){
          $tmp_answer = '';
          foreach($errors as $key => $error){
            if($tmp_answer != '')
              $tmp_answer .= "\\n\\n"; 
            $tmp_answer .= addslashes(str_replace("\n", "", str_replace("\r", "", $error)));
            
          }
          $answer .= 'alert(\''.$tmp_answer.'\')';
        }else{
          ModulesInstallation::install($_REQUEST['module_group'], $_REQUEST['module']);
          $answer .= '
                  window.location = \''.$cms->generateUrl().'\';
          ';
        }
      }
      echo $answer;
      \Db::disconnect();
      exit;
    }else{
      $this->standard_module->before_content = $this->find_new_modules();
      return $this->standard_module->manage();
    }
   }
  
  
   function find_new_modules(){
    global $cms;
    $answer = '';
    $new_modules = array();
    
    $new_module_groups = $this->get_folders(MODULE_DIR.'standard/content_management/widgets/');
    
    foreach($new_module_groups as $key  => $new_module_group){
      $new_module_groups[$key] = $this->get_folders(MODULE_DIR."standard/content_management/widgets/".$key."/");
    }
    
    $db_module_groups = Db::getModuleGroups();
    foreach($new_module_groups as $new_module_group_key => $new_module_group){
      foreach($new_module_group as $new_module_key => $new_module){
        if(!isset($db_module_groups[$new_module_group_key][$new_module_key]))
          $new_modules[] = array($new_module_group_key, $new_module_key);
      }
    }
    
    if(sizeof($new_modules) > 0)
      $answer .= '<link media="screen" rel="stylesheet" type="text/css" href="'.BASE_URL.MODULE_DIR.'developer/widgets/style.css"/>';
    foreach($new_modules as $key => $new_module){
      $answer .= '
      <div class="newModule">
        <p>New module detected: <b>'.htmlspecialchars($new_module[0]).'/'.htmlspecialchars($new_module[1]).'</b></p>
        <a onclick="LibDefault.ajaxMessage(\''.$cms->generateUrl().'\', \'type=ajax&action=install&module_group='.$new_module[0].'&module='.$new_module[1].'\')" class="button">Install</a>
        <div class="clear"></div>
      </div>
      
      ';
    }
    return $answer;
   }
   
   function get_folders($dir){
    $answer = array();
    if(file_exists($dir) && is_dir($dir)){
      $handle = opendir($dir);
      if($handle !== false){
         while (false !== ($file = readdir($handle))) {
          if(is_dir($dir.$file) && $file != '..' && $file != '.')
           $answer[$file] = array();
         }
         return $answer;
      }
    }
   }  

}
