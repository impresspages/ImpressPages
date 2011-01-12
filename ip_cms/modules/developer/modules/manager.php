<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\developer\modules;

if (!defined('BACKEND')) exit;

require_once (BASE_DIR.LIBRARY_DIR.'php/standard_module/std_mod.php');
require_once (__DIR__.'/db.php');
require_once (__DIR__.'/installation.php');


class ModulesArea extends \Library\Php\StandardModule\Area{


  var $lastError;
  
  function last_error($action){
    return $this->lastError;
  }
  
  function after_delete($id){
    Db::deletePermissions($id);
    
  }

  function allow_delete($id, $deletingArea, $deletingId){
    global $parametersMod;
    
    $module = \Db::getModule($id);
    
    $ignoreGroupId = null;
    if($deletingArea->db_table == 'module_group'){
      $ignoreGroupId = $deletingId;
    }

    $error = $this->getDeleteError($module['g_name'], $module['m_name'], $ignoreGroupId);
    
    if($error){
      $this->lastError = $error;  
      return false;
    }
    if ($module['core']) {
      $this->lastError = $parametersMod->getValue('developer', 'modules', 'admin_translations', 'error_cant_delete_core_module').' '.$module['g_name'].'/'.$module['m_name'];
      return false;
    } else {
      return true;
    }
  }
  
  
  public function getDeleteError($moduleGroupKey, $moduleKey, $ignoreGroupId = null){
    global $parametersMod;
    
    $moduleGroups = \Backend\Db::modules();
    foreach($moduleGroups as $group){
      foreach($group as $module){
        if($module['g_id'] != $ignoreGroupId){
          if(!$module['core']){
            $configuration = new ConfigurationFile(BASE_DIR.PLUGIN_DIR.$module['g_name'].'/'.$module['m_name'].'/install/plugin.ini');
            if($configuration){
              foreach($configuration->getRequiredModules() as $requiredModule){
                if($requiredModule['module_group_key'] == $moduleGroupKey && $requiredModule['module_key'] == $moduleKey){
                  $error = $parametersMod->getValue('developer', 'modules', 'admin_translations', 'error_delete_required_module').' '.$module['g_name'].'/'.$module['m_name'];
                  return $error;
                }
              }          
            }
            
          }
        }
      }
    }
  }  

  function before_delete($id){
    $module = \Db::getModule($id);
    if (!$module['core']) {
      Db::deletePermissions($id);
      if(file_exists(BASE_DIR.PLUGIN_DIR.$module['g_name'].'/'.$module['m_name'].'/uninstall/script.php')){
        require_once(BASE_DIR.PLUGIN_DIR.$module['g_name'].'/'.$module['m_name'].'/uninstall/script.php');
        eval('$uninstallObject = new \\Modules\\'.$module['g_name'].'\\'.$module['m_name'].'\\Uninstall();');
        $uninstallObject->execute();
      }
    }
  }

  public static function after_insert($id){
    global $cms;
    Db::addPermissions($id, $cms->session->userId());
    Db::newModuleRowNumber($id);
  }
}


class ModulesGroupArea extends \Library\Php\StandardModule\Area{
  public static function after_insert($id){
    Db::newModuleGroupRowNumber($id);
  }
}

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
    $elements[] = $element;
    $tmp_el = $element;



    $element = new \Library\Php\StandardModule\element_text("text");
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
    $element->db_field = "name";
    $element->show_on_list = true;
    $element->reg_expression = "/^[A-Za-z0-9\-_]+$/";
    $element->reg_expression_for_user = $parametersMod->getValue('developer','modules_configuration','admin_translations','error_incorrect_name');
    //   $element->searchable = true;
    $elements[] = $element;
     
    $element = new \Library\Php\StandardModule\element_bool();
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
    $element->db_field = "admin";
    $element->show_on_list = true;
    // $element->searchable = true;
    $elements[] = $element;
     

    $area0 = new ModulesGroupArea();
    $area0->db_table = "module_group";
    $area0->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','module_groups');
    $area0->db_key = "id";
    $area0->elements = $elements;
    $area0->sort_field = "row_number";
    $area0->sortable = true;
    $area0->order_by = "row_number";
    $area0->name_element = $tmp_el;


    $elements = array();

    $element = new \Library\Php\StandardModule\element_text("text");
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
    $element->db_field = "translation";
    $element->show_on_list = true;
    $element->searchable = true;
    $elements[] = $element;
    $tmp_el = $element;

    $element = new \Library\Php\StandardModule\element_text("text");
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
    $element->db_field = "name";
    $element->show_on_list = true;
    $element->reg_expression = "/^[A-Za-z0-9\-_]+$/";
    $element->reg_expression_for_user = $parametersMod->getValue('developer','modules_configuration','admin_translations','error_incorrect_name');
    //   $element->searchable = true;
    $elements[] = $element;


    $element = new \Library\Php\StandardModule\element_bool();
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','admin');
    $element->db_field = "admin";
    $element->show_on_list = true;
    // $element->searchable = true;
    $elements[] = $element;

    $element = new \Library\Php\StandardModule\element_bool();
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','managed');
    $element->db_field = "managed";
    $element->show_on_list = true;
    // $element->searchable = true;
    $elements[] = $element;


    $element = new \Library\Php\StandardModule\element_bool();
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','core');
    $element->db_field = "core";
    $element->show_on_list = true;
    // $element->searchable = true;
    $elements[] = $element;

    $element = new \Library\Php\StandardModule\element_text();
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','version');
    $element->db_field = "version";
    $element->reg_expression = $parametersMod->getValue('developer', 'std_mod','parameters','number_real_reg_expression');
    $element->reg_expression_for_user = '';
    $element->show_on_list = true;
    // $element->searchable = true;
    $elements[] = $element;
    

    $area1 = new ModulesArea();
    $area1->db_table = "module";
    $area1->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','modules');
    $area1->db_key = "id";
    $area1->elements = $elements;
    $area1->sort_field = "row_number";
    $area1->db_reference = "group_id";
    $area1->sortable = true;
    $area1->searchable = true;
    $area1->order_by = "row_number";
    $area1->name_element = $tmp_el;
     








    //==============================================





    $elements = array();





    $element = new \Library\Php\StandardModule\element_text();
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','key');
    $element->db_field = "name";
    $element->reg_expression = "/^[A-Za-z0-9\-_]+$/";
    $element->show_on_list = true;
    // $element->searchable = true;
    $elements[] = $element;
    	
    $element = new \Library\Php\StandardModule\element_text("text");
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','name');
    $element->db_field = "translation";
    $element->show_on_list = true;
    $element->translation_field = "module_parameters_group_id";
    // $element->searchable = true;
    $elements[] = $element;
    $tmp_el = $element;



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
    $area2->order_by = 'row_number';
    $area2->name_element = $tmp_el;
    $area2->visible = false;



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
    $element->show_on_list = true;
    // $element->searchable = true;
    $elements[] = $element;




    $element = new \Library\Php\StandardModule\element_parameter();
    $element->name = $parametersMod->getValue('developer', 'modules_configuration','admin_translations','value');
    $element->db_field = "id";
    $element->show_on_list = true;
    // $element->searchable = true;
    $elements[] = $element;


    /*     $element = new element_text();
     $element->name = $parametersMod->getValue('developer', 'modules_configuration','translations','regexpression');
     $element->db_field = "regexpression";
     $element->show_on_list = true;
     // $element->searchable = true;
     $elements[] = $element;
     */
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
    $area3->order_by = 'row_number';
    $area3->name_element = $tmp_el;
    	
    	
    	
    $area2->set_area($area3);
    $area1->set_area($area2);
    $area0->set_area($area1);


    $this->standardModule = new \Library\Php\StandardModule\StandardModule($area0, 0);
     
  }
  function manage(){
    global $cms;
    $answer = '';
    if(isset($_REQUEST['type']) == 'ajax' && $_REQUEST['action'] == 'install'){
      if($_REQUEST['action'] == 'install'){
        $installation = new \Modules\developer\modules\ModulesInstallation();
        $errors = $installation->getErrors($_REQUEST['module_group'], $_REQUEST['module']);
        if($errors){
          $tmp_answer = '';
          foreach($errors as $key => $error){
            if($tmp_answer != '')
            $tmp_answer .= "\\n\\n";
            $tmp_answer .= addslashes(str_replace("\n", "", str_replace("\r", "", $error)));

          }
          $answer .= 'alert(\''.$tmp_answer.'\')';
        }else{
          $installation->recursiveInstall($_REQUEST['module_group'], $_REQUEST['module']);
          $answer .= '
            window.location = \''.$cms->generateUrl().'\';
          ';
        }
      }
      echo $answer;
      \Db::disconnect();
      exit;
    }else{
      $installation = new \Modules\developer\modules\ModulesInstallation();
      $this->standardModule->before_content = $installation->findNewModules();
      return $this->standardModule->manage();
    }
  }


   
}
