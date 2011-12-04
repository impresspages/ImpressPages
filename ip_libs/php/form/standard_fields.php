<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Library\Php\Form;

if (!defined('CMS')) exit;
/**
 * General class of form fields.
 * @package Library
 */
class Field{
    /** @var string field caption. Visible near the input field*/
    var $caption;
    /** @var string field post name*/
    var $name;
    /** @var bool true if field can't be leaved blank*/
    var $required;
    /** @var string field name in database. Usd to automaticaly write or update records in database*/
    var $dbField;
    /** @var string default value of field*/
    var $value;
    /** @var string explanation for user about what should be writen in this input field. Showed when user hover the mouse over the field.*/
    var $hint;
    /** @var string Same as note, but is visible allways*/
    var $note;

    /**
     * @return mixed value that is posted trought this field
     */
    function postedValue(){
        return $_POST[$this->name];
    }

    /**
     * @return bool true if the field should be refreshed on user browser after submit.
     */
    function renewRequired(){
        return false;
    }

    function getError(){
        return false;
    }
}

/**
 * @package Library
 */
class FieldText extends Field{
    var $disableAutocomplete = false;

    function genHtml($class, $id){
        if ($this->disableAutocomplete) {
            $disableAutocomplete = ' autocomplete="off" ';
        } else {
            $disableAutocomplete = '';
        }

        $answer = '';
        if(isset($_POST[$this->name]))
        return '<input id="'.$id.'" class="'.$class.'" type="text" name="'.$this->name.'" value="'.htmlspecialchars($_POST[$this->name]).'" '.$disableAutocomplete.'/>'."\n";
        else
        return '<input id="'.$id.'" class="'.$class.'" type="text" name="'.$this->name.'" value="'.htmlspecialchars($this->value).'" '.$disableAutocomplete.'/>'."\n";
    }
    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '' )){
            $error = true;
        }
        return $error;
    }
}

/**
 * @package Library
 */
class FieldCheckbox extends Field{
    function genHtml($class, $id){
        $answer = '';
        if(isset($_POST[$this->name]) || $this->value)
        return '<input id="'.$id.'" checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'" value="1"/>'."\n";
        else
        return '<input id="'.$id.'" class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'"/>'."\n";
    }
    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '' )){
            $error = true;
        }
        return $error;
    }
    function postedValue(){
        if(isset($_POST[$this->name]))
        return 1;
        else
        return 0;
    }

}

/**
 * @package Library
 */
class FieldPassword extends Field{
    var $disableAutocomplete = false;
    function genHtml($class, $id){
        $answer = '';

        if ($this->disableAutocomplete) {
            $disableAutocomplete = ' autocomplete="off" ';
        } else {
            $disableAutocomplete = '';
        }


        if(isset($_POST[$this->name]))
        return '<input id="'.$id.'" class="'.$class.'" type="password" name="'.$this->name.'" value="'.htmlspecialchars($_POST[$this->name]).'" '.$disableAutocomplete.' />'."\n";
        else
        return '<input id="'.$id.'" class="'.$class.'" type="password" name="'.$this->name.'" value="'.htmlspecialchars($this->value).'" '.$disableAutocomplete.' />'."\n";
    }
    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '' )){
            $error = true;
        }
        return $error;
    }
}

/**
 * @package Library
 */
class FieldTextarea extends Field{
    function genHtml($class, $id){
        if(isset($_POST[$this->name]))
        return '<textarea id="'.$id.'" cols="25" rows="5"  class="'.$class.'" name="'.$this->name.'" >'.$_POST[$this->name].'</textarea>'."\n";
        else
        return '<textarea id="'.$id.'" cols="25" rows="5" class="'.$class.'" name="'.$this->name.'" >'.htmlspecialchars($this->value).'</textarea>'."\n";
    }
    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '' )){
            $error = true;
        }
        return $error;
    }
}

/**
 * @package Library
 */
class FieldFile extends Field{
    function genHtml($class, $id){
        return '<input id="'.$id.'" class="'.$class.'" type="file" name="'.$this->name.'" />'."\n";
    }
    function getError(){
        $error = false;
        if($this->required && (!isset($_FILES[$this->name]['tmp_name']) || $_FILES[$this->name]['error'] !=  UPLOAD_ERR_OK   )){
            $error = true;
        }
        return $error;
    }
    function postedValue(){
        return $_FILES[$this->name]['name'];
    }
     
}


/**
 * @package Library
 */
class FieldHidden extends Field{
    function genHtml($class, $id){
        return '<input id="'.$id.'" class="'.$class.'" type="hidden" name="'.$this->name.'" value="'.htmlspecialchars($this->value).'"/>'."\n";
    }
    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '' )){
            $error = true;
        }
        return $error;
    }
}

/**
 * @package Library
 */
class FieldSelect extends Field{
    var $values;
    function genHtml($class, $id){
        $answer = '
<select id="'.$id.'" class="'.$class.'" name="'.$this->name.'">'."\n";
        foreach($this->values as $key => $value){
            if($this->value == $value[0])
            $answer .= '  <option selected value="'.$value[0].'">'.htmlspecialchars($value[1]).'</option>'."\n";
            else
            $answer .= '  <option value="'.$value[0].'">'.htmlspecialchars($value[1]).'</option>'."\n";
        }
        $answer .= '
</select>'."\n";
        return $answer;
    }
    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '') ){
            $error = true;
        }else{
            $exists = false;
            foreach($this->values as $key => $value)
            if($value[0] == $_POST[$this->name])
            $exists = true;
            if(!$exists)
            $error = true;
        }
        return $error;
    }
}



/**
 * @package Library
 */
class FieldEmail extends Field{
    function genHtml($class, $id){
        if(isset($_POST[$this->name]))
        return '<input id="'.$id.'" class="'.$class.'" type="text" name="'.$this->name.'" value="'.htmlspecialchars($_POST[$this->name]).'"/>'."\n";
        else
        return '<input id="'.$id.'" class="'.$class.'" type="text" name="'.$this->name.'" value="'.htmlspecialchars($this->value).'"/>'."\n";
    }
    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '' )){
            $error = true;
        }
        if(isset($_POST[$this->name]) && $_POST[$this->name] != '' && !preg_match('#^[a-z0-9.!\#$%&\'*+-/=?^_`{|}~]+@([0-9.]+|([^\s]+\.+[a-z]{2,6}))$#si', $_POST[$this->name])){
            $error = true;
        }
        return $error;
    }
}




/**
 * @package Library
 */
class FieldWysiwyg extends Field{
    function genHtml($class, $id){
        $answer = '
<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tiny_mce/paste_function.js"></script>          

<script type="text/javascript">
//<![CDATA[ 
  tinyMCE.init({
    theme : "advanced",
    mode: "exact",
  	elements: "'.$id.'",
  	plugins: "paste,inlinepopups", 
  	theme_advanced_buttons1 : "copy,paste,pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
  	theme_advanced_buttons2 : "bold,italic,underline,styleselect",
  	theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup",
  	theme_advanced_toolbar_location : "top",
  	theme_advanced_toolbar_align : "left",	
    //	theme_advanced_resizing  : true,
    //	theme_advanced_resize_horizontal : true,
  	theme_advanced_path_location : "none",
  	content_css : "design/style.css",	
  	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
  	height : "300",
  	content_css : "'.BASE_URL.THEME_DIR.THEME.'/" + "ip_content.css",
  	theme_advanced_styles : "Text=;Caption=caption;Signature=signature;Note=note",
  	forced_root_block : "p",
  	
    paste_auto_cleanup_on_paste : true,
    paste_retain_style_properties: false,
    paste_strip_class_attributes: true,
    paste_remove_spans: true,
    paste_remove_styles: true,
    paste_convert_middot_lists: true,
    
    paste_preprocess : ip_paste_preprocess_function				
  });
  tinyMCE.execCommand("mceAddControl", true, "'.$this->name.'");
//]]>    
</script>
    ';

        if(isset($_POST[$this->name]))
        $answer .= '<textarea id="'.$id.'"  name="'.$this->name.'">'.$_POST[$this->name].'</textarea>'."\n";
        else
        $answer .= '<textarea id="'.$id.'"  name="'.$this->name.'">'.$this->value.'</textarea>'."\n";
        return $answer;
    }

    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '' )){
            $error = true;
        }
        return $error;
    }
}




/**
 * @package Library
 */
class FieldCaptcha extends Field{
    var $captcha_init;


    function __construct(){

        $this->captcha_init = array(

        // string: absolute path (with trailing slash!) to a php-writeable tempfolder which is also accessible via HTTP!
      'tempfolder'     => BASE_DIR.TMP_IMAGE_DIR,

        // string: absolute path (in filesystem, with trailing slash!) to folder which contain your TrueType-Fontfiles.
      'TTF_folder'     => BASE_DIR.LIBRARY_DIR.'php/form/hn_captcha/fonts/',

        // mixed (array or string): basename(s) of TrueType-Fontfiles, OR the string 'AUTO'. AUTO scanns the TTF_folder for files ending with '.ttf' and include them in an Array.
        // Attention, the names have to be written casesensitive!
        //'TTF_RANGE'    => 'NewRoman.ttf',
        //'TTF_RANGE'    => 'AUTO',
        //'TTF_RANGE'    => array('actionj.ttf','bboron.ttf','epilog.ttf','fresnel.ttf','lexo.ttf','tetanus.ttf','thisprty.ttf','tomnr.ttf'),
      'TTF_RANGE'    => 'AUTO',

      'chars'          => 5,       // integer: number of chars to use for ID
      'minsize'        => 20,      // integer: minimal size of chars
      'maxsize'        => 30,      // integer: maximal size of chars
      'maxrotation'    => 25,      // integer: define the maximal angle for char-rotation, good results are between 0 and 30
      'use_only_md5'   => FALSE,   // boolean: use chars from 0-9 and A-F, or 0-9 and A-Z

      'noise'          => TRUE,    // boolean: TRUE = noisy chars | FALSE = grid
      'websafecolors'  => FALSE,   // boolean
      'refreshlink'    => TRUE,    // boolean
      'lang'           => 'en',    // string:  ['en'|'de'|'fr'|'it'|'fi']
      'maxtry'         => 3,       // integer: [1-9]

      'badguys_url'    => '/',     // string: URL
      'secretstring'   => md5(DB_PASSWORD),//'A very, very secret string which is used to generate a md5-key!',
      'secretposition' => 9        // integer: [1-32]
        );

    }

    function genHtml($class, $id){
        require_once(__DIR__.'/hn_captcha/hn_captcha.class.php');
        $captcha = new hn_captcha($this->captcha_init, TRUE);

        $captcha->make_captcha();

        $_SESSION['library']['php']['form']['standard']['captcha'][$this->name]['public_key'] = $captcha->public_key;
        return '
<img src="'.BASE_URL.$captcha->get_filename_url().'" alt="Captcha"/><br />
<input id="'.$id.'" type="text" name="'.$this->name.'" />
';
        //<input type="hidden" name="'.$this->name.'_captcha_key" value="'.$captcha->public_key.'" />';

    }


    function getError(){
        require_once(__DIR__.'/hn_captcha/hn_captcha.class.php');
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '')){
            $error = true;
        }

        $captcha = new hn_captcha($this->captcha_init, TRUE);

        if(strtolower($_POST[$this->name])!== strtolower($captcha->generate_private($_SESSION['library']['php']['form']['standard']['captcha'][$this->name]['public_key']))){
            $error = true;
        }
        return $error;
    }

    function renewRequired(){
        return $this->getError();

    }
}


/**
 * @package Library
 */
class FieldRadio extends Field{
    var $values;

    public function __construct() {
        $this->value = null;
    }

    function genHtml($class, $id){
        $answer = '';

        if($this->value && empty($values)) { //old approach
            if(isset($_POST[$this->name]))
            return '<input id="'.$id.'" class="'.$class.'" type="text" name="'.$this->name.'" value="'.htmlspecialchars($_POST[$this->name]).'"/>'."\n";
            else
            return '<input id="'.$id.'" class="'.$class.'" type="text" name="'.$this->name.'" value="'.htmlspecialchars($this->value).'"/>'."\n";
        } else { //new approach
            foreach ($this->values as $key => $value) {
                $answer .= '<div class="libPhpFormFieldRow">';
                if(isset($_POST[$this->name]) && $_POST[$this->name] == $this->value)
                $answer .= '<input id="'.$id.'_'.$key.'" value="'.addslashes($value[0]).'" checked="checked" class="'.$class.' radio" type="radio" name="'.$this->name.'" value="1"/> <label for="'.$id.'_'.$key.'">'.htmlspecialchars($value[1]).'</label>'."\n";
                else
                $answer .= '<input id="'.$id.'_'.$key.'" value="'.addslashes($value[0]).'" class="'.$class.' radio" type="radio" name="'.$this->name.'"/> <label for="'.$id.'_'.$key.'">'.htmlspecialchars($value[1]).'</label>'."\n";
                $answer .= '</div>';
            }
        }
        return $answer;
    }

    function getError(){
        $error = false;
        if($this->required && (!isset($_POST[$this->name]) || $_POST[$this->name] == '' )){
            $error = true;
        }
        return $error;
    }
    function postedValue(){
        if(isset($_POST[$this->name]))
        return 1;
        else
        return 0;
    }

    function getName($str){
        $underscorePos = strpos($str, '_');
        if($underscorePos !== false){
            return substr($str, 0, $underscorePos);
        }
    }
}