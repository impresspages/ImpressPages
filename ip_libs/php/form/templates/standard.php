<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Library\Php\Form\Templates;  
 
 if (!defined('CMS')) exit;

require_once (__DIR__.'/../standard_fields.php');

/**
 * Class to generate common form template.
 * @package Library 
 */  
class Standard{
  public static function generateForm($button, $action, $uniqueName, $fields){
    $answer = '';
      
    $answer .= '
<script type="text/javascript">
  //<![CDATA[
  /* Get the TOP position of a given element. */
  function '.$uniqueName.'GetPositionTop(element){
    var offset = 0;
    while(element) {
      offset += element["offsetTop"];
      element = element.offsetParent;
    }
    return offset;
  }
  
  /* Is a given element visible or not? */
  function '.$uniqueName.'IsElementVisible(eltId) {
    var elt = document.getElementById(eltId);
    if (!elt) {
        // Element not found.
        return false;
    }
    // Get the top and bottom position of the given element.
    var posTop = '.$uniqueName.'GetPositionTop(elt);
    var posBottom = posTop + elt.offsetHeight;
    // Get the top and bottom position of the *visible* part of the window.
    var visibleTop = document.documentElement.scrollTop;
    var visibleBottom = visibleTop + document.documentElement.offsetHeight;
    return ((posBottom >= visibleTop) && (posTop <= visibleBottom));
  } 


  
  function '.$uniqueName.'_reset(field_name){
    //document.getElementById(\''.$uniqueName.'_field_\' + field_name).setAttribute("class", "libPhpFormField");
    //document.getElementById(\''.$uniqueName.'_field_\' + field_name).setAttribute("className", "libPhpFormField");
    document.getElementById(\''.$uniqueName.'_field_\' + field_name).className = "libPhpFormField";
    document.getElementById(\''.$uniqueName.'_field_\' + field_name + \'_error\').innerHTML = \'\';
    document.getElementById(\''.$uniqueName.'_field_\' + field_name + \'_error\').style.display = \'none\';
    document.getElementById(\''.$uniqueName.'_global_error\').style.display = \'none\';
  }
  
  function '.$uniqueName.'_set_error(field_name, error, first){
    //document.getElementById(\''.$uniqueName.'_field_\' + field_name).setAttribute("class", "libPhpFormFieldError");
    //document.getElementById(\''.$uniqueName.'_field_\' + field_name).setAttribute("className", "libPhpFormFieldError");
    document.getElementById(\''.$uniqueName.'_field_\' + field_name).className = "libPhpFormFieldError";
    if(error != \'\'){
      document.getElementById(\''.$uniqueName.'_field_\' + field_name + \'_error\').innerHTML = error;
      document.getElementById(\''.$uniqueName.'_field_\' + field_name + \'_error\').style.display = \'block\';
    }
    
    if(first && !'.$uniqueName.'IsElementVisible(\''.$uniqueName.'_field_\' + field_name)){
      document.location = \'#'.$uniqueName.'_field_\' + field_name + \'_error_anchor\';
    }
  }
  
  function '.$uniqueName.'_set_global_error(error, first){
    document.getElementById(\''.$uniqueName.'_global_error\').innerHTML = error;
    document.getElementById(\''.$uniqueName.'_global_error\').style.display = \'block\';
    if(first && !'.$uniqueName.'IsElementVisible(\''.$uniqueName.'_global_error\')){
      document.location = \'#'.$uniqueName.'_global_error_anchor\';
    }
    
  }
  
  function '.$uniqueName.'_replace_input(field_name, new_html){
    document.getElementById(\''.$uniqueName.'_field_\' + field_name + \'_input\').innerHTML = new_html;
  }
  //]]>    
</script>     
    ';

    $tmp_html = '';
    foreach($fields as $key => $field){
      $tmp_html .= self::generateField($field, $uniqueName);
    }
    
    
    $answer .= '
<div>        
  <a name="'.$uniqueName.'_global_error_anchor">
  </a>
  <div id="'.$uniqueName.'_global_error" class="libPhpFormGlobalError">
  </div>
  '.$tmp_html.'
  <div class="clear"><!-- --></div>
  <div class="libPhpFormButtons">
    <input type="submit" class="libPhpFormSubmit" value="'.htmlspecialchars($button).'" />
  </div>
  <div class="clear"><!-- --></div>
</div>
';
    
    
  

    $answer .='

      ';    
      
    return $answer;
  }
  
   
  public static function generateField($field, $uniqueName){
    $answer = '';
    
    if($field->required)
      $f_required_prefix = ' *';
    else
      $f_required_prefix = '';

    if($field->hint){
      $fHintBlock = '
<div class="libPhpFormHint" id="'.$uniqueName.'_hint_'.$field->name.'">
  <span>
    '.$field->hint.'
  </span>
</div>';
      $fHintScript = '
onmouseover="document.getElementById(\''.$uniqueName.'_hint_'.$field->name.'\').style.display=\'block\'"
onmouseout="document.getElementById(\''.$uniqueName.'_hint_'.$field->name.'\').style.display=\'none\'"
';
    }else{
      $fHintBlock = '';
      $fHintScript = '';              
    }

    if($field->note)
      $fNoteBlock = '
<div class="libPhpFormNote">
  '.$field->note.'
</div>
';
    else
      $fNoteBlock = '';
  
    $answer .= '
<div id="'.$uniqueName.'_field_'.$field->name.'" class="libPhpFormField" '.$fHintScript.'>
  <a name="'.$uniqueName.'_field_'.$field->name.'_error_anchor">
  </a>
  <label for="'.$uniqueName.'_field_'.$field->name.'_input_field" class="libPhpFormCaption">
    '.$field->caption.$f_required_prefix.'
  </label>
  <div class="libPhpFormInput">
    <div id="'.$uniqueName.'_field_'.$field->name.'_input">
      '.$field->genHtml('formField', $uniqueName.'_field_'.$field->name.'_input_field').'
    </div>
    <div id="'.$uniqueName.'_field_'.$field->name.'_error" class="libPhpFormError">
    </div>
    '.$fNoteBlock.'
  </div>
  '.$fHintBlock.'
  <div class="clear"></div>
</div>';
    return $answer;  
  }
}




