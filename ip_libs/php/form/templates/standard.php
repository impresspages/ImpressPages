<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Library\Php\Form\Templates;

if (!defined('CMS')) exit;

require_once \Ip\Config::libraryFile('php/form/standard_fields.php');

/**
 * Class to generate common form template.
 * @package Library
 */
class Standard{
    public static function generateForm($button, $action, $uniqueName, $fields){
        $answer = '';



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




