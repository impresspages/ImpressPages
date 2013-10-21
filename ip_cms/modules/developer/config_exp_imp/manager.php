<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\developer\config_exp_imp;


require_once (__DIR__.'/html_output.php');
require_once (__DIR__.'/parameters.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/file/functions.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/file/upload_file.php');
require_once (BASE_DIR.LIBRARY_DIR.'php/form/standard.php');
require_once (__DIR__.'/additional_standard_form_fields.php');
require_once (BASE_DIR.MODULE_DIR.'developer/localization/manager.php');


class Manager{
    private $htmlOutput;
    private $importFields;

    function __construct(){
        global $parametersMod;

        //-----import felds


        $this->importFields = array();

        $field = new \Library\Php\Form\FieldFile();
        $field->name = 'config';
        $field->caption = $parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'config_file');
        $field->required = true;
        $this->importFields[] = $field;

        //-----export felds

        $this->exportFields = array();


        $field = new FieldModules();
        $field->name = 'modules';
        $field->caption = $parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'modules');
        $field->required = true;
        $this->exportFields[]  = $field;

        $field = new FieldTypes();
        $field->name = 'types';
        $field->caption = $parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'types');
        $field->required = true;
        $this->exportFields[]  = $field;

        $field = new FieldLanguages();
        $field->name = 'language';
        $field->caption = $parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'language');
        $field->required = true;
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
                        $fileUpload->allowOnly(array("php", "conf", "txt"));
                        $file = $fileUpload->upload('config', TMP_FILE_DIR);
                        if($file == UPLOAD_ERR_OK){
                            $_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file'] = BASE_DIR.TMP_FILE_DIR.$fileUpload->fileName;
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
                    $info = pathinfo($_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file']);
                    if($info['extension'] == 'conf'){
                        $answer .= HtmlOutput::header();
                        $config = unserialize(file_get_contents($_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file']));
                        $answer .= '<h1>'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_title_preview')).'</h1>';
                        $answer .= '<br /><a href="'.$cms->generateUrl($cms->curModId, 'action=import_confirmed').'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_button_preview')).'</a><br /><br /><br />';
                        $answer .= $config->previewParameters();
                        $answer .= '<br /><a href="'.$cms->generateUrl($cms->curModId, 'action=import_confirmed').'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_button_preview')).'</a><br /><br />';
                        $answer .= HtmlOutput::footer();
                    } else {
                        $answer .= HtmlOutput::header();
                        $answer .= '<h1>'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_title_preview')).'</h1>';
                        $answer .= '<br /><a href="'.$cms->generateUrl($cms->curModId, 'action=import_confirmed').'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_button_preview')).'</a><br /><br /><br />';
                        $answer .= \Modules\developer\localization\Manager::previewParameters($_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file']);
                        $answer .= '<br /><a href="'.$cms->generateUrl($cms->curModId, 'action=import_confirmed').'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_button_preview')).'</a><br /><br />';
                        $answer .= HtmlOutput::footer();
                        $answer .= HtmlOutput::header();
                    }
                    break;
                case 'import_confirmed':
                    if(isset($_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file'])){
                        $info = pathinfo($_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file']);
                        if($info['extension'] == 'conf'){
                            $config = unserialize(file_get_contents($_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file']));
                            $answer .= HtmlOutput::header();
                            //$config_import = new mod_developer_config_exp_imp_parameters();
                            //$config_import->save_parameters();
                            $config->saveParameters();
                            $answer .= '
                <div class="content">
                  <h1>'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'parameters_imported')).'</h1>
                  <a href="'.$cms->generateUrl($cms->curModId).'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'continue')).'</a>
                  <div class="clear"><!-- --></div>
                </div>
              ';
                            $answer .= HtmlOutput::footer();

                        } else {
                            $answer .= HtmlOutput::header();
                            //$config_import = new mod_developer_config_exp_imp_parameters();
                            //$config_import->save_parameters();
                            \Modules\developer\localization\Manager::saveParameters($_SESSION['backend_modules']['developer']['config_exp_imp']['uploaded_file']);
                            $answer .=  '
              <div class="content">
                <h1>'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'parameters_imported')).'</h1>
                <a href="'.$cms->generateUrl($cms->curModId).'" class="button">'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'continue')).'</a>
                <div class="clear">&nbsp;<!-- --></div>
              </div>
                ';
                            $answer .= HtmlOutput::footer();

                        }

                    }
                    break;
                case 'export':
                    $standardForm = new \Library\Php\Form\Standard($this->exportFields);
                    $errors = $standardForm->getErrors();


                    if(sizeof($errors) > 0){
                        $answer .= HtmlOutput::header();
                        $answer .= $standardForm->generateErrorAnswer($errors);
                        $answer .= HtmlOutput::footer();
                    }else{
                        $file = $this->writeParametersToFile();
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=\"".$file."\"");
                        $answer = (file_get_contents(TMP_FILE_DIR.$file));
                    }


                    break;
            }
        }else{
            $answer .= HtmlOutput::header();
            $answer .= '<div class="content">';
            $answer .= '<h1>'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_title')).'</h1>';
            $answer .= $this->importForm();
            $answer .= '</div><div class="content">';
            $answer .= '<h1>'.htmlspecialchars($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'export_title')).'</h1>';
            $answer .= $this->exportForm();
            $answer .= '</div>';
             
            $answer .= HtmlOutput::footer();
        }

        return $answer;
    }


    public function previewParameters($file){
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
    private function importForm(){
        global $parametersMod;
        global $cms;
        $answer = '';

        $import_form = new \Library\Php\Form\Standard($this->importFields);
        $answer .= ''.$import_form->generateForm($parametersMod->getValue('developer', 'config_exp_imp', 'admin_translations', 'import_button'), $cms->generateUrl($cms->curModId, 'action=import'));


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




    private function writeParametersToFile(){
        $parameters = new Parameters($_POST['language'], $_POST['types']);
        $fileName = '';
        foreach($_POST['modules'] as $groupKey => $group){
            foreach ($group as $moduleKey => $value){
                if($fileName == '')
                $fileName = "ip_".htmlspecialchars($groupKey)."_".htmlspecialchars($moduleKey)." ".date("Y-m-d").".php";
                $parameters->loadParameters($value);
            }
        }

        $fileName = \Library\Php\File\Functions::genUnoccupiedName($fileName, TMP_FILE_DIR);
        $fh = fopen(TMP_FILE_DIR.$fileName, 'w');
        if($fh){
            fwrite($fh, $this->generateConfigurationFile($parameters));
            fclose($fh);
            return $fileName;
        }else trigger_error("can't open file ".$TMP_FILE_DIR.$fileName);

        return false;
    }


    private function generateConfigurationFile($parameters){
        global $parametersMod;
        $answer = '';

        $answer .= '<?php
//language description
$languageCode = "'.str_replace('"', '\\"', $parameters->cachedLanguage['code']).'"; //RFC 4646 code
$languageShort = "'.str_replace('"', '\\"', $parameters->cachedLanguage['d_short']).'"; //Short description
$languageLong = "'.str_replace('"', '\\"', $parameters->cachedLanguage['d_long']).'"; //Long title
$languageUrl = "'.str_replace('"', '\\"', $parameters->cachedLanguage['url']).'";
';    

        $usedModuleTitles = array();
        $usedParameterGroupTitle = array();

        foreach($parameters->parameterGroups as $parameterGroup){
            if(!isset($usedModuleTitles[$parameterGroup->moduleGroupName][$parameterGroup->moduleGroupName])){
                $answer .=
'

$moduleGroupTitle["'.$parameterGroup->moduleGroupName.'"] = "'.str_replace('"', '\\"', $parameterGroup->moduleGroupTranslation).'";
$moduleTitle["'.$parameterGroup->moduleGroupName.'"]["'.$parameterGroup->moduleName.'"] = "'.str_replace('"', '\\"', $parameterGroup->moduleTranslation).'";';  
                $usedModuleTitles[$parameterGroup->moduleGroupName][$parameterGroup->moduleGroupName] = true;
            }
            foreach($parameterGroup->parameters as $parameter){
                //create parameter group
                if(!isset($usedParameterGroupTitle[$parameterGroup->moduleGroupName][$parameterGroup->moduleGroupName][$parameterGroup->name])){
                    $answer .=
  '
  
  $parameterGroupTitle["'.$parameterGroup->moduleGroupName.'"]["'.$parameterGroup->moduleName.'"]["'.$parameterGroup->name.'"] = "'.str_replace('"', '\\"', $parameterGroup->translation).'";
  $parameterGroupAdmin["'.$parameterGroup->moduleGroupName.'"]["'.$parameterGroup->moduleName.'"]["'.$parameterGroup->name.'"] = "'.str_replace('"', '\\"', $parameterGroup->admin).'";';
                    $usedParameterGroupTitle[$parameterGroup->moduleGroupName][$parameterGroup->moduleGroupName][$parameterGroup->name] = true;
                }



                //create parameter

                $answer .=
'

    $parameterTitle["'.$parameterGroup->moduleGroupName.'"]["'.$parameterGroup->moduleName.'"]["'.$parameterGroup->name.'"]["'.$parameter['name'].'"] = "'.str_replace('"', '\\"', $parameter['translation']).'";
    $parameterValue["'.$parameterGroup->moduleGroupName.'"]["'.$parameterGroup->moduleName.'"]["'.$parameterGroup->name.'"]["'.$parameter['name'].'"] = "'.str_replace('"', '\\"', $parameter['value']).'";
    $parameterAdmin["'.$parameterGroup->moduleGroupName.'"]["'.$parameterGroup->moduleName.'"]["'.$parameterGroup->name.'"]["'.$parameter['name'].'"] = "'.$parameter['admin'].'";
    $parameterType["'.$parameterGroup->moduleGroupName.'"]["'.$parameterGroup->moduleName.'"]["'.$parameterGroup->name.'"]["'.$parameter['name'].'"] = "'.str_replace('"', '\\"', $parameter['type']).'";';

            }
        }


        return $answer;


    }
}


