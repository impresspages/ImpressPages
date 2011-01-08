<?php

/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\developer\localization;

if (!defined('BACKEND')) exit;  

require_once (__DIR__.'/html_output.php');
require_once (__DIR__.'/db.php');

require_once (BASE_DIR.LIBRARY_DIR.'php/file/functions.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/file/upload_file.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/form/standard.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/form/standard_fields.php');
require_once (__DIR__.'/additional_standard_form_fields.php');

class Manager{
  
  function __construct(){
   global $parametersMod; 
    
    //-----import felds

    
    $this->importFields = array();
    
    $field = new \Library\Php\Form\FieldFile();
    $field->name = 'config';
    //$field->caption = $parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'config_file');
    $field->required = false;
    $this->importFields[] = $field;
    
    //-----export fields
    
    $this->exportFields = array();
    
    


        
    /*$field = new \Library\Php\Form\FieldSelect();
    $field->name = 'type';
    $field->caption = $parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'language');
    $values = array();  
    
    $values[] = array('backend', $parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'language'));
    $values[] = array('frontend', $parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'language'));

    $field->values = $values;
    $field->required = true;
    $this->exportFields[]  = $field;*/

        
    $field = new FieldLanguages();
    $field->name = 'language';
    $field->caption = "";
    $field->required = false;
    $this->exportFields[]  = $field;
    
  }
  
  function manage(){
    global $cms;
    global $parametersMod;
    
    $answer = '';
    if(isset($_GET['action'])){
      switch($_GET['action']){
        case 'import':
          $standardForm = new \Library\Php\Form\Standard($this->importFields);
          $errors = $standardForm->getErrors();
          
          if(sizeof($errors) > 0)  
            $answer = $standardForm->generateErrorAnswer($errors);
          else{
            $fileUpload = new \Library\Php\File\UploadFile();
            $fileUpload->allowOnly(array("php"));
            $file = $fileUpload->upload('config', TMP_FILE_DIR); 
            if($file == UPLOAD_ERR_OK){
              $_SESSION['backend_modules']['developer']['localization']['uploaded_file'] = BASE_DIR.TMP_FILE_DIR.$fileUpload->fileName;
              $answer .= HtmlOutput::header();
              $answer .= '
                <script type="text/javascript">
                  //<![CDATA[
                  parent.document.location = \''.$cms->generateUrl($cms->curModId, 'action=import_uploaded').'\';
                  //]]
                </script>';
              $answer .= HtmlOutput::footer();              
            }else{
              $errors['config'] = 'impossible to upload';
              $answer .= HtmlOutput::header();
              $answer .= $standardForm->generateErrorAnswer($errors);
              $answer .= HtmlOutput::footer(); 
            }
          }        
        break;
        case 'import_uploaded':
              $answer .= HtmlOutput::header();
              $answer .= '<h1>'.htmlspecialchars($parametersMod->getValue('developer', 'localization', 'admin_translations', 'preview')).'</h1>';
              $answer .= '<br /><a href="'.$cms->generateUrl($cms->curModId, 'action=import_confirmed').'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'localization', 'admin_translations', 'import_language_file')).'</a><br /><br /><br />';
              $answer .= $this->previewParameters($_SESSION['backend_modules']['developer']['localization']['uploaded_file']);
              $answer .= '<br /><a href="'.$cms->generateUrl($cms->curModId, 'action=import_confirmed').'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'localization', 'admin_translations', 'import_language_file')).'</a><br /><br />';
              $answer .= HtmlOutput::footer(); 
        break;
        case 'import_confirmed':
          if(isset($_SESSION['backend_modules']['developer']['localization']['uploaded_file'])){
            //$config = unserialize(file_get_contents(TMP_FILE_DIR.$_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file']));            
            $answer .= HtmlOutput::header();
            //$config_import = new mod_developer_config_exp_imp_parameters();
            //$config_import->save_parameters();
            $this->saveParameters($_SESSION['backend_modules']['developer']['localization']['uploaded_file']);
            $answer .= '
              <div class="content">
                <h1>'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'parameters_imported')).'</h1>
                <a href="'.$cms->generateUrl($cms->curModId).'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'continue')).'</a>
                <div class="clear">&nbsp;<!-- --></div>
              </div>
            ';
            $answer .= HtmlOutput::footer(); 
          
          }
        break;        
        case 'export':
          if($_REQUEST['language'] == 'backend'){
            $standardForm = new \Library\Php\Form\Standard($this->exportFields);
            $errors = $standardForm->getErrors();
            
            if(sizeof($errors) > 0){  
              $answer .= HtmlOutput::header();
              $answer .= $standardForm->generateErrorAnswer($errors);
              $answer .= HtmlOutput::footer(); 
            }else{
              header("Content-type: application/octet-stream");
              header("Content-Disposition: attachment; filename=\"administrator_interface_".$parametersMod->getValue('standard', 'configuration','advanced_options','administrator_interface_language').".php\"");
              $answer = $this->generateAdministratorInterfaceLanguageFile();
            }
          } else {
            $standardForm = new \Library\Php\Form\Standard($this->exportFields);
            $errors = $standardForm->getErrors();
            
            if(sizeof($errors) > 0){  
              $answer .= HtmlOutput::header();
              $answer .= $standardForm->generateErrorAnswer($errors);
              $answer .= HtmlOutput::footer(); 
            }else{
              header("Content-type: application/octet-stream");
              $language = Db::getLanguage($_REQUEST['language']);
              header("Content-Disposition: attachment; filename=\"public_interface_".$language['code'].".php\"");
              $answer = $this->generatePublicInterfaceLanguageFile($_REQUEST['language']);
            }

          }
        break;
      }
    }else{
      $answer .= HtmlOutput::header();
      $answer .= '<div class="content">'; 
      $answer .= '<h1>'.htmlspecialchars($parametersMod->getValue('developer', 'localization', 'admin_translations', 'import_language_file')).'</h1>';
      $answer .= $this->importForm();
      $answer .= '</div><div class="content">';
      $answer .= '<h1>'.htmlspecialchars($parametersMod->getValue('developer', 'localization', 'admin_translations', 'export_language_file')).'</h1>';
      $answer .= $this->exportForm();
      $answer .= '</div>';      
      
      $answer .= HtmlOutput::footer(); 
    }
    
    return $answer;
  }
  
  public static function saveParameters($file, $ignoreLanguage = false){
    require_once(MODULE_DIR.'standard/languages/db.php');
    
    //require_once(MODULE_DIR."standard/seo/db.php");
    global $parametersMod;
    global $site;
    require($file);
    $answer = '';
    
    //get languageId
    $languageId = null;
    if($ignoreLanguage || !isset($languageCode)){
      $languageId = $site->currentLanguage['id'];
    } else {
      $languages = \Modules\standard\languages\db::getLanguages();
      foreach($languages as $key => $language){
        if($language['code'] == $languageCode)
          $languageId = $language['id'];
      }
      if($languageId === null){
        if(!isset($languageShort))
          $languageShort = $languageCode;
        if(!isset($languageLong))
          $languageLong = $languageCode;
        if(!isset($languageUrl))
          $languageUrl = $languageCode;
          
        $parameter = array();
        
        $rowNumber = -99999999999999999;
        $languages = \Modules\standard\languages\Db::getLanguages();
        foreach($languages as $language){
          if($language['row_number'] >= $rowNumber){
            $rowNumber = $language['row_number'] + 1;
          }
        }
        
        
        $languageId = Db::insertLanguage($languageCode, $languageShort, $languageLong, $languageUrl, 0, $rowNumber);
        \Modules\standard\languages\Db::createRootZoneElement($languageId);
        \Modules\standard\languages\Db::createEmptyTranslations($languageId, 'par_lang');
      }
    }
    
    
    if(isset($parameterGroupTitle)){
      foreach($parameterGroupTitle as $groupName => $group){
        foreach($group as $moduleName => $module){
          foreach($module as $parameterGroupName => $value){
            $tmpModule = \Db::getModule(null, $groupName, $moduleName);
            if($tmpModule){
              $tmpParameterGroup = Db::getParameterGroup($tmpModule['id'], $parameterGroupName);      
              if($tmpParameterGroup) {
                Db::setParameterGroupTitle($tmpParameterGroup['id'], $value);
              }else{
                if(isset($parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName]))
                  $admin = $parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName];
                else
                  $admin = 1;
                $tmpParameterGroup = Db::createParameterGroup($tmpModule['id'], $parameterGroupName, $value, $admin);
              }            
            }
          }
        }
      }
    }      
    
    
    if(isset($parameterValue)){
      foreach($parameterValue as $groupName => $moduleGroup){
        foreach($moduleGroup as $moduleName => $module){
          $tmpModule = \Db::getModule(null, $groupName, $moduleName);
          if($tmpModule){
            foreach($module as $parameterGroupName => $parameterGroup){
              $tmpParameterGroup = Db::getParameterGroup($tmpModule['id'], $parameterGroupName);      
              if(!$tmpParameterGroup) {
                if(isset($parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName]))
                  $admin = $parameterGroupAdmin[$groupName][$moduleName][$parameterGroupName];
                else
                  $admin = 1;
                $tmpParameterGroup['id'] = Db::createParameterGroup($tmpModule['id'], $parameterGroupName, $parameterGroupName, $admin);
              }            
  
              foreach($parameterGroup as $parameterName => $value){
                if(!$parametersMod->exist($groupName, $moduleName, $parameterGroupName, $parameterName)){
                  $parameter = array();
                  $parameter['name'] = $parameterName;
                  if(isset($parameterAdmin[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                    $parameter['admin'] = $parameterAdmin[$groupName][$moduleName][$parameterGroupName][$parameterName];
                  else
                    $parameter['admin'] = 1;
                    
                  if(isset($parameterTitle[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                    $parameter['translation'] = $parameterTitle[$groupName][$moduleName][$parameterGroupName][$parameterName];
                  else
                    $parameter['translation'] = $parameterName;
                    
                  if(isset($parameterType[$groupName][$moduleName][$parameterGroupName][$parameterName]))
                    $parameter['type'] = $parameterType[$groupName][$moduleName][$parameterGroupName][$parameterName];
                  else
                    $parameter['type'] = 'string';
                    
                  $parameter['value'] = str_replace("\r\n", "\n", $value); //user can edit parameters file and change line endings. So, we convert them back
                  $parameter['value'] = str_replace("\r", "\n", $parameter['value']);
                  Db::insertParameter($tmpParameterGroup['id'], $parameter);
                } else {
                  $value = str_replace("\r\n", "\n", $value); //user can edit parameters file and change line endings. So, we convert them back
                  $value = str_replace("\r", "\n", $value);
                  $parametersMod->setValue($groupName, $moduleName, $parameterGroupName, $parameterName, $value, $languageId);
                }
              }
            }

          }
        }
      }
    }
    

    if(isset($parameterTitle)){
      foreach($parameterTitle as $moduleGroupName => $moduleGroup){
        foreach($moduleGroup as $moduleName => $module){
          foreach($module as $parameterGroupName => $parameterGroup){
             foreach($parameterGroup as $parameterName => $title){
               Db::setParameterTitle($moduleGroupName, $moduleName, $parameterGroupName, $parameterName, $title);
             }
          }
        }
      }
    }
    
    
    if(isset($moduleGroupTitle)){
      foreach($moduleGroupTitle as $groupName => $value){
        Db::setModuleGroupTitle($groupName, $value);
      }
    }    
    
    
    
    if(isset($moduleTitle)){
      foreach($moduleTitle as $groupName => $group){
        foreach($group as $moduleName => $value){
          Db::setModuleTitle($groupName, $moduleName, $value);
        }
      }
    }
    
    

    return $answer;
  }
    
  
  public static function previewParameters($file){
    require($file);
    $answer = '';
    
    $preparedParameters = array();
    if(isset($parameterValue))    
    foreach($parameterValue as $moduleGroupName => $moduleGroup){
      foreach($moduleGroup as $moduleName => $module){
        foreach($module as $parameterGroupName => $parameterGroup){
           foreach($parameterGroup as $parameterName => $value){
             $preparedParameters[$moduleGroupName][$moduleName][$parameterGroupName][$parameterName]['value'] = $value;
           }
        }
      }
    }
    
    if(isset($parameterTitle))    
    foreach($parameterTitle as $moduleGroupName => $moduleGroup){
      foreach($moduleGroup as $moduleName => $module){
        foreach($module as $parameterGroupName => $parameterGroup){
           foreach($parameterGroup as $parameterName => $title){
             $preparedParameters[$moduleGroupName][$moduleName][$parameterGroupName][$parameterName]['title'] = $title;
           }
        }
      }
    }
    

    foreach($preparedParameters as $moduleGroupName => $moduleGroup){
      $answer .= '<div class="content">';
      if(isset($moduleGroupTitle[$moduleGroupName]))
        $moduleGroupTranslation = $moduleGroupTitle[$moduleGroupName];
      else 
        $moduleGroupTranslation = $moduleGroupName; 
      
      foreach($moduleGroup as $moduleName => $module){
        if(isset($moduleTitle[$moduleGroupName][$moduleName]))
          $moduleTranslation = $moduleTitle[$moduleGroupName][$moduleName];
        else
          $moduleTranslation = $moduleName;
        
        $answer .= '<h1>'.htmlspecialchars($moduleGroupTranslation.' -> '.$moduleTranslation).'</h1>';
        foreach($module as $parameterGroupName => $parameterGroup){
          if(isset($parameterGroupTitle[$moduleGroupName][$moduleName][$parameterGroupName]))
            $parameterGroupTranslation = $parameterGroupTitle[$moduleGroupName][$moduleName][$parameterGroupName];
          else
            $parameterGroupTranslation = $parameterGroupName;
          
          $answer .= '<h2>'.htmlspecialchars($parameterGroupTranslation).'</h2>';
          foreach($parameterGroup as $parameterName => $parameter){
            if(!isset($parameter['title']))
              $parameter['title'] = '';
              
            if(!isset($parameter['value']))
              $parameter['value'] = '';
            //if(sizeof($this->parameters) > 0){
              //foreach($this->parameters as $parameterKey => $parameter){
                $answer .= '<div class="parameter"><div class="parameterName">'.htmlspecialchars($parameter['title']).'</div> <div class="parameterValue">'.htmlspecialchars($parameter['value']).'</div><div class="clear"></div></div>';
             
           }
        }
      }
      $answer .= '</div>';
    }
    
    

    return $answer;
  }
  
  private function generatePublicInterfaceLanguageFile($languageId){
    global $parametersMod;
    require_once(__DIR__."/public_interface_config.php");
    
    $answer = '';

    $language = Db::getLanguage($languageId);
    
    $answer .= 
'<?php

//language description
$languageCode = "'.str_replace('"', '\\"', $language['code']).'"; //RFC 4646 code
$languageShort = "'.str_replace('"', '\\"', $language['d_short']).'"; //Short description
$languageLong = "'.str_replace('"', '\\"', $language['d_long']).'"; //Long title
$languageUrl = "'.str_replace('"', '\\"', $language['url']).'";



';

    //parameters
    $lastParameter = '';
    foreach($parameter as $key => $par){
      $keys = explode("/", $key);
      if($lastParameter != $keys[1].'/'.$keys[2].'/'.$keys[3].'/'.$keys[4]){
        $answer .=
'
';
      }
      $lastParameter = $keys[1].'/'.$keys[2].'/'.$keys[3].'/'.$keys[4];
        
      
      if($keys[0] == 'parameterValue'){
        $answer .= 
'
$parameterValue["'.$keys[1].'"]["'.$keys[2].'"]["'.$keys[3].'"]["'.$keys[4].'"] = "'.str_replace('"', '\\"', $parametersMod->getValue($keys[1], $keys[2], $keys[3], $keys[4], $languageId)).'";';
      }           
    }
    
    
    return $answer;
    
  }   
  
  private function generateAdministratorInterfaceLanguageFile(){
    global $parametersMod;
    require_once(__DIR__."/administrator_interface_config.php");
    
    $answer = '';
    
    //language code
    $answer .= 
'<?php
$parameterValue ["standard"]["configuration"]["advanced_options"]["administrator_interface_language"] = "'.str_replace('"', '\\"', $parametersMod->getValue('standard', 'configuration','advanced_options','administrator_interface_language')).'"; //insert RFC 4646 code of language into whish you are translating now

';
    
    //module groups
    foreach($moduleGroupTitle as $key => $groupTitle){
      $group = Db::getModuleGroup($key);
      $answer .= 
'
$moduleGroupTitle["'.$key.'"] = "'.str_replace('"', '\\"', $group['translation']).'";';  
    
    }

$answer .= 
'
';    
    
    //modules
    foreach($moduleTitle as $groupName => $group){
      foreach($group as $moduleName => $group){
        $module = Db::getModule($groupName, $moduleName);
        $answer .= 
'
$moduleTitle["'.$groupName.'"]["'.$moduleName.'"] = "'.str_replace('"', '\\"', $module['translation']).'";';  
      }
    }

    
$answer .= 
'
';    
    
    //parameter groups
    foreach($parameterGroupTitle as $moduleGroupName => $group){
      foreach($group as $moduleName => $module){
        foreach($module as $parameterGroupName => $parameterGroup){
          $module = Db::getModule($moduleGroupName, $moduleName);
          $moduleGroup = Db::getParameterGroup($module['id'], $parameterGroupName);
          $answer .= 
'
$parameterGroupTitle["'.$moduleGroupName.'"]["'.$moduleName.'"]["'.$parameterGroupName.'"] = "'.str_replace('"', '\\"', $moduleGroup['translation']).'";';
        }  
      }
    }
    
    
    //parameters
    $lastParameter = '';
    foreach($parameter as $key => $par){
      $keys = explode("/", $key);
      if($lastParameter != $keys[1].'/'.$keys[2].'/'.$keys[3].'/'.$keys[4]){
        $answer .=
'
';
      }
      $lastParameter = $keys[1].'/'.$keys[2].'/'.$keys[3].'/'.$keys[4];
        
      
      if($keys[0] == 'parameterTitle'){
        $dbParameter = Db::getParameter($keys[1], $keys[2], $keys[3], $keys[4]);
        $answer .= 
'
$parameterTitle["'.$keys[1].'"]["'.$keys[2].'"]["'.$keys[3].'"]["'.$keys[4].'"] = "'.str_replace('"', '\\"', $dbParameter['translation']).'";';
      } else {
        $answer .= 
'
$parameterValue["'.$keys[1].'"]["'.$keys[2].'"]["'.$keys[3].'"]["'.$keys[4].'"] = "'.str_replace('"', '\\"', $parametersMod->getValue($keys[1], $keys[2], $keys[3], $keys[4])).'";';
      }           
    }
    
    
    return $answer;
    
  }  
  
  
  private function exportForm(){
    global $parametersMod;
    global $cms;
    $answer = '';

    $export_form = new \Library\Php\Form\Standard($this->exportFields);
    $answer .= $export_form->generateForm($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'export_button'), $cms->generateUrl($cms->curModId, 'action=export'));

    return $answer;  
  }  
  
  private function importForm(){
    global $parametersMod;
    global $cms;
    $answer = '';

    $import_form = new \Library\Php\Form\Standard($this->importFields);
    $answer .= ''.$import_form->generateForm($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_button'), $cms->generateUrl($cms->curModId, 'action=import'));


    return $answer;  
  }
  

}


