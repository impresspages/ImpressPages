<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\contact_form;

if (!defined('CMS')) exit;

const GROUP_KEY = 'misc';
const MODULE_KEY = 'contact_form';

require_once(\BASE_DIR.\LIBRARY_DIR.'php/js/functions.php');

class Module extends \Modules\standard\content_management\Widget{

  function init(){
    global $site; 
    $answer = '
      <script type="text/javascript" src="'.BASE_URL.CONTENT_MODULE_URL.'misc/contact_form/module.js"></script>
      <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/forms.js"></script>
    
    ';
     
    
    $site->requireConfig('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/config.php');

    $layouts = Config::getLayouts();
    
    $script = '';
    if(!isset($layouts) || sizeof($layouts) == 0){
      $layouts = array();
      $layouts[] = array('translation'=>'', 'name'=>'default');
    }
    
    foreach($layouts as $key => $layout){
      $script .= '<option value="'.addslashes($layout['name']).'" >'.addslashes($layout['translation']).'</option>';
    }
    
    if(sizeof($layouts) <=1)
      $script = '<div class="ipCmsModuleLayout hidden"><label class="ipCmsTitle">Layout: </label><select name="layout">'.$script.'</select></div>';
    else
      $script = '<div class="ipCmsModuleLayout"><label class="ipCmsTitle">Layout: </label><select name="layout">'.$script.'</select></div>';
    
    $answer .= '
    <script type="text/javascript" >
    //<![CDATA[
    mod_contact_form_layout = \''.$script.'\';
     //]]>
    </script>
    ';
     
     
    return $answer;



  }
   
  function getLayout($id){
    $sql = "select * from `".DB_PREF."mc_misc_contact_form` where `id` = '".(int)$id."'";
    $rs = mysql_query($sql);
    if($rs){
      if($lock = mysql_fetch_assoc($rs)){
        $layout = $lock['layout'];
        return $layout;
      }
    } else {
      trigger_error($sql.' '.mysql_error());
    }
    return false;
  }
   

  function add_to_modules($mod_management_name, $collection_number, $module_id, $visible){ //add existing module from database to javascript array
    global $site;
    $site->requireTemplate('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/template.php');
     
    $sql = "select button, thank_you, email_to, email_subject from `".DB_PREF."mc_misc_contact_form` where `id` = '".(int)$module_id."' ";
    $rs = mysql_query($sql);
    if (!$rs || !$lock = mysql_fetch_assoc($rs))
    trigger_error("Can't get module information ".$sql);
    else{
      $thank_you = $lock['thank_you'];
      $button = $lock['button'];
      $email_to = $lock['email_to'];
      $email_subject = $lock['email_subject'];
      $answer = "";
      $answer .= '<script type="text/javascript">
                  //<![CDATA[
                  ';
      $answer .= "  var new_module = new content_mod_contact_form();";
      //       $answer .= "  var new_module_name = '".$mod_management_name."' + ".$mod_management_name.".get_modules_array_name() + '[' + ".$mod_management_name.".get_modules.length + ']'; alert('AAA' + new_module_name);";
      $answer .= "  var new_module_name = '".$mod_management_name.".' + ".$mod_management_name.".get_modules_array_name() + '[".$collection_number."]';";
      $answer .= "  new_module.init(".$collection_number.", ".$module_id.", ".$visible.", new_module_name, ".$mod_management_name.");";
      $answer .= "  new_module.fields = new Array();";

      $fields = array();
      $sql = "select * from `".DB_PREF."mc_misc_contact_form_field` where `contact_form` = '".(int)$module_id."' order by id asc";
      $rs = mysql_query($sql);
      if(!$rs)
      $this->set_error("Can't get form fields ".$sql);
      else{
        while($lock = mysql_fetch_assoc($rs)){
          $answer .= "  var field = new Array();";
          $answer .= "  field[0] = '".addslashes($lock['name'])."';";
          $answer .= "  field[1] = '".addslashes($lock['type'])."';";
          $answer .= "  field[2] = ".addslashes($lock['required']).";";
          $fieldValues = json_decode($lock['values']);
          if($fieldValues){
              $fieldValues = \Library\Php\Js\Functions::htmlToString(implode("\n", $fieldValues));
          }
          else
          {
              $fieldValues = '';
          }
          $answer .= "  field[3] = '".$fieldValues."';";
          $answer .= "  new_module.fields.push(field);";
          $answer .= "  ";
           
           
          switch($lock['type']){
            case 'text':
              $field = new \Library\Php\Form\FieldText();
              break;
            case 'text_multiline':
              $field = new \Library\Php\Form\FieldTextarea();
              break;
            case 'file':
              $field = new \Library\Php\Form\FieldFile();
              break;
            case 'email':
              $field = new \Library\Php\Form\FieldEmail();
              break;
            case 'select':
                $field = new \Library\Php\Form\FieldSelect();
                $field->values = $this->prepare_for_select($this->db_values_to_array($lock['values']));
                break;
            case 'checkbox':
                $field = new \Library\Php\Form\FieldCheckbox();
                break;
            case 'radio':
                $field = new \Library\Php\Form\FieldRadio();
                $field->values = $this->prepare_for_select($this->db_values_to_array($lock['values']));
                break;
          }
          $field->caption = $lock['name'];
          $field->name = 'field_'.(sizeof($fields) + 1);
          $field->required = $lock['required'];
          $fields[] = $field;
        }
      }
      $answer .= "  new_module.preview_html = '".str_replace('script',"scr' + 'ipt", str_replace("\r", "", str_replace("\n", "' + \n '", str_replace("'", "\\'",Template::generateHtml($fields,$thank_you, $email_to, $button, $email_subject, 'col_'.$collection_number, $this->getLayout($module_id))))))."';";
      $answer .= "  new_module.layout = '".str_replace("\r", "", str_replace("\n", "' + \n '", str_replace("'", "\\'",$this->getLayout($module_id))))."';";
       
       
      $answer .= "  new_module.thank_you = '".addslashes($thank_you)."';";
      $answer .= "  new_module.button = '".addslashes($button)."';";
      $answer .= "  new_module.email_to = '".addslashes($email_to)."';";
      $answer .= "  new_module.email_subject = '".addslashes($email_subject)."';";
      $answer .= "  new_module.set_contact_form('".$lock['contact_form']."');";
      $answer .= "  new_module.set_contact_form('".$lock['contact_form']."');";
       
      $answer .= "  ".$mod_management_name.".get_modules().push(new_module);";
      $answer .= "  ";
      $answer .= "  ";
      $answer .= "//]]>";
      $answer .= "</script>";
       

       
       
    }
    return $answer;
  }

  function makeActions(){
    global $parametersMod;

    global $site;
    $site->requireTemplate('standard/content_management/widgets/misc/contact_form/template.php');
     
     
    $sql = "select * from `".DB_PREF."mc_misc_contact_form_field` where `contact_form` = '".mysql_real_escape_string($_REQUEST['spec_id'])."' order by id";
    $rs = mysql_query($sql);
    $fields = array();
    if(!$rs)
    trigger_error("Can't get form fields ".$sql);
    else{
      while($lock = mysql_fetch_assoc($rs)){
        switch($lock['type']){
          case 'text':
            $field = new \Library\Php\Form\FieldText();
            break;
          case 'text_multiline':
            $field = new \Library\Php\Form\FieldTextarea();
            break;
          case 'file':
            $field = new \Library\Php\Form\FieldFile();
            break;
          case 'email':
            $field = new \Library\Php\Form\FieldEmail();
            break;
          case 'select':
            $field = new \Library\Php\Form\FieldSelect();
            $field->values = $this->prepare_for_select($this->db_values_to_array($lock['values']));
            break;
          case 'checkbox':
            $field = new \Library\Php\Form\FieldCheckbox();
            break;
          case 'radio':
            $field = new \Library\Php\Form\FieldRadio();
            $field->values = $this->prepare_for_select($this->db_values_to_array($lock['values']));
            break;
        }
        $field->caption = $lock['name'];
        $field->name = 'field_'.(sizeof($fields) + 1);
        $field->required = $lock['required'];
        $fields[] = $field;
      }

    }
     
    $htmlForm = new \Library\Php\Form\Standard($fields);
    $errors = $htmlForm->getErrors();
    $answer = '';
    if(sizeof($errors) > 0 || $htmlForm->detectSpam()){
      $answer = $htmlForm->generateErrorAnswer($errors);

    }else{

      $sql = "select * from `".DB_PREF."mc_misc_contact_form` where `id` = '".mysql_real_escape_string($_REQUEST['spec_id'])."' limit 1";
      $rs = mysql_query($sql);
      if(!$rs)
      trigger_error("Can't get contact form ".$sql);
      elseif($lock = mysql_fetch_assoc($rs)){
        $email = Template::generateEmail($fields);
        $files = array();
        $from = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email');
        foreach($fields as $field){
          if(get_class($field) == 'Library\\Php\\Form\\FieldFile'){
            if ((!empty($_FILES[$field->name])) && ($_FILES[$field->name]['error'] == 0)) {
              $file = array();
              $file['real_name'] = $_FILES[$field->name]['tmp_name'];
              $file['required_name'] = $_FILES[$field->name]['name'];
              $files[] = $file;
            }
          }

          if(get_class($field) == 'Library\\Php\\Form\\FieldEmail' && $field->postedValue() != ''){
            $from = $field->postedValue();
          }
        }


        $email_queue = new \Modules\administrator\email_queue\Module();
        $email_queue->addEmail($from, '', $lock['email_to'], '',  $lock['email_subject'], $email, false, true, $files);


      }else{
        trigger_error("Unknown contact form id ".$sql);
      }

      $email_queue->send();
      $answer = '
            <html><head><meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'" /></head><body>
            <script type="text/javascript">
              var form = parent.window.document.getElementById(\''.$_REQUEST['spec_rand_name'].'\');
              form.style.display = \'none\';
              //var txtNode = document.createTextNode("'.htmlspecialchars($lock['thank_you']).'"); 
              var note = parent.window.document.createElement(\'p\');
              //note.appendChild(txtNode); //ie7 bug sometimes dont allow to add a textnode. 
              note.innerHTML = "'.htmlspecialchars($lock['thank_you']).'";
              form.parentNode.appendChild(note);
            </script>
            </body></html>
          ';
    }

    echo $answer;

  }

  function create_new_instance($values){
    $sql = "insert into `".DB_PREF."mc_misc_contact_form` set layout= '".mysql_real_escape_string($values['layout'])."', button='".mysql_real_escape_string($values['button'])."', thank_you = '".mysql_real_escape_string($values['thank_you'])."', email_to = '".mysql_real_escape_string($values['email_to'])."' ,email_subject = '".mysql_real_escape_string($values['email_subject'])."'  ";
    $rs = mysql_query($sql);
    if(!$rs){
      $this->set_error("Can't insert new module. ".$sql);
    }else{
      $max_id = mysql_insert_id();
      $sql = "insert into `".DB_PREF."content_element_to_modules` set".
        " row_number = '".(int)$values['row_number']."', element_id = '".(int)$values['content_element_id']."' ".
        ", group_key='misc', module_key='contact_form', module_id = '".(int)$max_id."'".
        ", visible= '".(int)$values['visible']."' ";
      $rs = mysql_query($sql);
      if (!$rs){
        $this->set_error("Can't asociate element to module ".$sql);
        trigger_error("Can't asociate element to module  ".$sql);
      }
      $i = 0;
      while(isset($values['field_'.$i.'_name'])){

        $values_array = $this->values_to_array($values['field_'.$i.'_values']);

        $sql = "insert into `".DB_PREF."mc_misc_contact_form_field` set".
          " name = '".mysql_real_escape_string($values['field_'.$i.'_name'])."', 
          type = '".mysql_real_escape_string($values['field_'.$i.'_type'])."',
          `values` = '".mysql_real_escape_string(json_encode($values_array))."',
          required = '".mysql_real_escape_string($values['field_'.$i.'_required'])."',
          contact_form = '".$max_id."'";
        $rs = mysql_query($sql);
        if (!$rs){
          trigger_error("Can't insert field ".$sql);
          kalabasek();
          $this->set_error("Can't insert field ".$sql);
        }
        $i++;
      }
       
    }
  }

  function update($values){
    $sql = "update `".DB_PREF."content_element_to_modules` set `visible`='".(int)$values['visible']."', `row_number` = '".(int)$values['row_number']."' where `module_id` = '".(int)$values['id']."'  and `group_key` = '".mysql_real_escape_string(GROUP_KEY)."' and `module_key` = '".mysql_real_escape_string(MODULE_KEY)."'   ";
    if (!mysql_query($sql))
    return("Can't update module row number".$sql);
    else{
      $sql = "update `".DB_PREF."mc_misc_contact_form` set `layout`= '".mysql_real_escape_string($values['layout'])."' , `button`='".mysql_real_escape_string($values['button'])."', `thank_you` = '".mysql_real_escape_string($values['thank_you'])."', `email_to` = '".mysql_real_escape_string($values['email_to'])."' ,`email_subject` = '".mysql_real_escape_string($values['email_subject'])."'  where `id` = '".(int)$values['id']."' ";
      if (!mysql_query($sql))
      $this->set_error("Can't update module ".$sql);

      $sql = "delete from `".DB_PREF."mc_misc_contact_form_field` where `contact_form` = '".(int)$values['id']."'";
      $rs = mysql_query($sql);
      if (!mysql_query($sql))
      $this->set_error("Can't delete old fields ".$sql);
      $i = 0;
      while(isset($values['field_'.$i.'_name'])){

        $values_array = $this->values_to_array($values['field_'.$i.'_values']);


        $sql = "insert into `".DB_PREF."mc_misc_contact_form_field` set".
          " `name` = '".mysql_real_escape_string($values['field_'.$i.'_name'])."', 
          `type` = '".mysql_real_escape_string($values['field_'.$i.'_type'])."',
          `values` = '".mysql_real_escape_string(json_encode($values_array))."',
          `required` = '".mysql_real_escape_string($values['field_'.$i.'_required'])."',
          `contact_form` = '".(int)$values['id']."'";
        $rs = mysql_query($sql);
        if (!$rs)
        $this->set_error("Can't insert field ".$sql);
         
        $i++;
      }

    }
  }

  function delete($values){
    $sql = "delete from `".DB_PREF."mc_misc_contact_form_field` where `contact_form` = '".(int)$values['id']."'";
    if(!mysql_query($sql)){
      trigger_error("Can't delete contact form fields ".$sql);
    }else{
      $sql = "delete from `".DB_PREF."content_element_to_modules` where `module_id` = '".(int)$values['id']."'  and `group_key` = '".mysql_real_escape_string(GROUP_KEY)."' and `module_key` = '".mysql_real_escape_string(MODULE_KEY)."'";
      if (!mysql_query($sql))
      $this->set_error("Can't delete element to module association ".$sql);
      else{
        $sql = "delete from `".DB_PREF."mc_misc_contact_form` where `id` = '".(int)$values['id']."' ";
        if (!mysql_query($sql))
        $this->set_error("Can't delete module ".$sql);
      }
    }
  }


  function delete_by_id($id){
    $sql = "delete from `".DB_PREF."mc_misc_contact_form_field` where `contact_form` = '".(int)$id."'";
    if(!mysql_query($sql)){
      trigger_error("Can't delete contact form fields ".$sql);
    }else{
      $sql = "delete from `".DB_PREF."content_element_to_modules` where `module_id` = '".(int)$id."' and `group_key` = '".mysql_real_escape_string(GROUP_KEY)."' and `module_key` = '".mysql_real_escape_string(MODULE_KEY)."'";
      if (!mysql_query($sql))
      trigger_error("Can't delete element to module association ".$sql);
      else{
        $sql = "delete from `".DB_PREF."mc_misc_contact_form` where `id` = '".$id."' ";
        if (!mysql_query($sql))
        trigger_error("Can't delete module ".$sql);
      }
    }
  }



  function make_html($id){
    global $site;
    $site->requireTemplate('standard/content_management/widgets/misc/contact_form/template.php');
     
    $fields = array();
    $sql = "select * from `".DB_PREF."mc_misc_contact_form_field` where `contact_form` = '".(int)$id."' order by id asc";
    $rs = mysql_query($sql);
    if(!$rs)
    $this->set_error("Can't get form fields ".$sql);
    else{
      while($lock = mysql_fetch_assoc($rs)){
        switch($lock['type']){
          case 'text':
            $field = new \Library\Php\Form\FieldText();
            break;
          case 'text_multiline':
            $field = new \Library\Php\Form\FieldTextarea();
            break;
          case 'file':
            $field = new \Library\Php\Form\FieldFile();
            break;
          case 'email':
            $field = new \Library\Php\Form\FieldEmail();
            break;
          case 'select':
            $field = new \Library\Php\Form\FieldSelect();
            $field->values = $this->prepare_for_select($this->db_values_to_array($lock['values']));
            break;
          case 'checkbox':
            $field = new \Library\Php\Form\FieldCheckbox();
            break;
          case 'radio':
            $field = new \Library\Php\Form\FieldRadio();
            $field->values = $this->prepare_for_select($this->db_values_to_array($lock['values']));
            break;
        }
        $field->caption = $lock['name'];
        $field->name = 'field_'.(sizeof($fields) + 1);
        $field->required = $lock['required'];
        $fields[] = $field;
      }
    }
     
     
    $layout = $this->getLayout($id);

     
    $sql = "select thank_you, email_to, button, email_subject from `".DB_PREF."mc_misc_contact_form` where `id` = '".(int)$id."' ";
    $rs = mysql_query($sql);
    if ($rs){
      if ($lock = mysql_fetch_assoc($rs)){
        return Template::generateHtml($fields, $lock['thank_you'], $lock['email_to'], $lock['button'], $lock['email_subject'], $id, $layout);
      }
    }else
    trigger_error("Can't get text to create HTML ".$sql);
  }
  function manager_preview(){
    global $site;
    $site->requireTemplate('standard/content_management/widgets/'.GROUP_KEY.'/'.MODULE_KEY.'/template.php');
    $fields = array();
    $i=0;


    while(isset($_REQUEST['field_'.$i.'_name'])){

      switch($_REQUEST['field_'.$i.'_type']){
        case 'text':
          $field = new \Library\Php\Form\FieldText();
          break;
        case 'text_multiline':
          $field = new \Library\Php\Form\FieldTextarea();
          break;
        case 'file':
          $field = new \Library\Php\Form\FieldFile();
          break;
        case 'email':
          $field = new \Library\Php\Form\FieldEmail();
          break;
        case 'select':
          $field = new \Library\Php\Form\FieldSelect();
          $field->values = $this->prepare_for_select($this->values_to_array($_REQUEST['field_'.$i.'_values']));
          break;
        case 'checkbox':
          $field = new \Library\Php\Form\FieldCheckbox();
          break;
        case 'radio':
          $field = new \Library\Php\Form\FieldRadio();
          $field->values = $this->prepare_for_select($this->values_to_array($_REQUEST['field_'.$i.'_values']));
          break;
        default:
          trigger_error('Unknown type');
          break;
      }
      $field->caption = $_REQUEST['field_'.$i.'_name'];
      $field->name = 'field_'.(sizeof($fields) + 1);
      $field->required = $_REQUEST['field_'.$i.'_required'];
      $fields[] = $field;
      $i++;
    }
    $answer = Template::generateHtml($fields, $_REQUEST['thank_you'], $_REQUEST['email_to'], $_REQUEST['button'], $_REQUEST['email_subject'], 'col_'.$_REQUEST['collection_number'],  $_REQUEST['layout']); 
    return str_replace('document.write', '//document.write', $answer);
  }
   
  function is_dynamic(){
    return true;
  }
  function set_error($error){
    global $globalWorker;
    $globalWorker->set_error($error);
  }


  private function db_values_to_array($json_values)
  {
      if($json_values == ''){
          return array();
      } else {
          return json_decode($json_values);
      }
  }


  private function prepare_for_select($values)
  {
      $answer = array();
      foreach ($values as $key => $value) {
          $answer[] = array($value, $value);
      }
      return $answer;

  }


  private function values_to_array($txt_values)
  {
        $values['values'] = str_replace(array("\r\n"), "\n", $txt_values);
        $values['values'] = str_replace(array("\r"), "\n", $values['values']);
        $values_array = explode("\n", $values['values']);
        return $values_array;
  }


}

