<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\contact_form;   
 
if (!defined('CMS')) exit;

require_once (BASE_DIR.LIBRARY_DIR.'php/form/standard.php');

class Template {

  public static function generateHtml($fields, $thank_you, $email_to, $button, $email_subject, $id, $layout=null){
    
    global $site;
    global $module_url;
    global $log;

    
    switch($layout){
      default:
      case "default":    
        $answer = '';
        $field = '';
        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'cm_group';
        $field->value = 'misc';
        $fields[] = $field;
        
        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'cm_name';
        $field->value = 'contact_form';
        $fields[] = $field;
        
        
        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'action';
        $field->value = 'contact_form';
        $fields[] = $field;     
        
        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'module_group';
        $field->value = 'standard';
        $fields[] = $field;         
        
        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'spec_id';
        $field->value = $id;
        $fields[] = $field;  
        
        $field = new \Library\Php\Form\FieldHidden();
        $field->name = 'spec_url';
        $field->caption = 'URL';
        $field->value = $site->getCurrentUrl();
        $field->visible = false;
        $field->display = false;
        $fields[] = $field;

        $html_form = new \Library\Php\Form\Standard($fields);
        
        
        $answer .= $html_form->generateForm($button);
        
        return '
<div class="ipWidget ipWidgetContactForm">
  '.$answer.'
</div>
';
    }
  }
  
  public static function generateEmail($fields){
    require_once(BASE_DIR.LIBRARY_DIR.'php/text/system_variables.php');
    global $parametersMod;
    $content = '';
    for($i=0; $i<sizeof($fields); $i++){
      if (get_class($fields[$i]) != 'Library\\Php\\Form\\FieldHidden') {
        switch (get_class($fields[$i])) {
          case 'Library\\Php\\Form\\FieldEmail':
            $content .= '<b>'.htmlspecialchars($fields[$i]->caption).' :</b> <a href="mailto:'.nl2br(htmlspecialchars($fields[$i]->postedValue())).'">'.nl2br(htmlspecialchars($fields[$i]->postedValue())).'</a><br>'."\n";
          break;
          default:
            $content .= '<b>'.htmlspecialchars($fields[$i]->caption).' :</b> '.nl2br(htmlspecialchars($fields[$i]->postedValue())).'<br>'."\n";
          break;
        }
      }
    }

    if(isset($_POST['spec_url'])){
      $content .= '<b>URL :</b> <a href="'.nl2br($_POST['spec_url']).'">'.nl2br(htmlspecialchars($_POST['spec_url'])).'</a><br>'."\n";
    }


    $email = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email_template');
    $email = str_replace('[[content]]', $content, $email);
    
    $email = \Library\Php\Text\SystemVariables::insert($email);
    $email = \Library\Php\Text\SystemVariables::clear($email);

		$email = '
<html>
	<head></head>
	<body>
		'.$email.'
	</body>
</html>
';
    
    return $email;
  
  }
}

