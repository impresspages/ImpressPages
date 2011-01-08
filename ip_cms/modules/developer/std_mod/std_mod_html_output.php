<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see license.html
 */
namespace Modules\developer\std_mod;
 
if (!defined('BACKEND')) exit;

class stdModHtmlOutput{
  var $html;
  var $tabsId;
  var $tabId;
  var $tabsTmpHtml;
  var $tabTitles;
  var $tabContents;
  var $tinyMceFields;
  
  function __construct(){
    global $cms;
    $this->tinyMceFields = array();
  }
  
  function htmlOutput(){
    $this->html = '';
  }


  function fieldName($title){
    $this->html .= '<b>'.$title.'</b><br />';
  
  }
  
  //tabs
  function tabsOpen(){
    $this->tabsTmpHtml = $this->html;
		$this->html = '';
    $this->tabsId = rand(0, 1000000);
    $this->tabId = 0;
    
    $this->tabTitles = array();
    $this->tabContents = array();
    
  }
  
  function tabsClose(){
    $this->html = $this->tabsTmpHtml.'
    <script type="text/javascript">
      var stdMod'.$this->tabsId.' = new LibTabs(\'stdMod'.$this->tabsId.'\', \'modTabs\', \'modTabs2\');
    </script>
    ';
  
    $this->html .= '<div class="stdModTabs">';
    foreach($this->tabTitles as $key => $title){
      $this->html .= '<a id="stdModLink'.$this->tabsId.'At'.$key.'">'.htmlspecialchars($title).'</a> ';
    }
    $this->html .= '</div>';
    
    foreach($this->tabContents as $key => $content){
      $this->html .= '<div id="stdModCont'.$this->tabsId.'At'.$key.'">'.$content.'</div>';
      $this->html .= '
      <script type="text/javascript">
        stdMod'.$this->tabsId.'.addTab(\'stdModLink'.$this->tabsId.'At'.$key.'\', \'stdModCont'.$this->tabsId.'At'.$key.'\');
      </script>
      ';
    }

    $this->html .= '
    <script type="text/javascript">
      stdMod'.$this->tabsId.'.switchFirst();
    </script>';
    
    
  }

  function tabOpen($title){
    $this->tabTitles[$this->tabId] = $title;
  }
  
  function tabClose(){
    $this->tabContents[$this->tabId] = $this->html;
    $this->tabId++;
    $this->html = '';
  }  

  //eof tabs
  
  function label($label){
		$this->html .= '<span class="label">'.$label.'</span>';
	}
  
  //inputs
  
  function wysiwyg($name, $value = '', $disabled = false){
    global $site;
    $site->requireConfig('developer/std_mod/config.php');
    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';
  
  
    if($name == '')
      $this->html .= $this->error("Input without name ");
      global $cms;
      if(!$cms->tinyMce){
        $cms->tinyMce = true;
        $this->html .= '
          <script src="'.LIBRARY_DIR.'js/tiny_mce/tiny_mce.js"></script>
          <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/ext/adapter/prototype/prototype.js"></script>
          <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/tiny_mce/paste_function.js"></script>

          
          
        ';
          
        //tinymce styles
        global $site;
        $site->requireConfig('standard/content_management/config.php');
        $tinyMceStylesStr = '';
        foreach(\Modules\standard\content_management\Config::getMceStyles() as $style){
          if($tinyMceStylesStr != ''){
            $tinyMceStylesStr .= ';';
          }
          $tinyMceStylesStr .= $style['translation'].'='.$style['css_style'];
        }
        //end tinymce styles


            

        $this->html .= '
<script type="text/javascript">
  //<![CDATA[
  '.Config::getMceInit('mode: "specific_textareas"', 'editor_selector : "mceEditor"', 'backend').'
  //]]>
</script>
';

      }
      
          

    
    $this->html .= '<div class="stdMod"><textarea class="mceEditor" '.$disabledStr.' cols="100" rows="10" name="'.htmlspecialchars($name).'">'.$value.'</textarea></div>' ;
  }
  
  function input($name, $value = '', $disabled = false, $maxLength = null){
    if($name == '')
      $this->html .= $this->error("Input without name ");
    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';
      
    if($maxLength)
      $maxLengthStr = ' maxlength="'.$maxLength.'" ';
    else
      $maxLengthStr = '';
      
    $this->html .= '<input autocomplete="off" '.$maxLengthStr.'  '.$disabledStr.' class="stdMod" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'"/>' ;
  }
  
  function dateTime($name, $value = '', $disabled = false){
    if($name == '')
      $this->html .= $this->error("Input without name ");
    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';
      
    $this->html .= '<script src="'.BASE_URL.MODULE_DIR.'developer/std_mod/calendar/datetimepicker_css.js"></script>' ;
    $this->html .= '<script src="'.BASE_URL.MODULE_DIR.'developer/std_mod/calendar/datetimepicker_css.js"></script>' ;
    $this->html .= '<input id="'.htmlspecialchars($name).'" autocomplete="off" '.$disabledStr.' class="stdMod" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'"/>
    <a href="javascript: NewCssCal(\''.htmlspecialchars($name).'\',\'yyyymmdd\',\'dropdown\',true,24,false)"><img src="'.BASE_URL.MODULE_DIR.'developer/std_mod/calendar/images/cal.gif"" border="0"/></a>' ;
  }
    
  function date($name, $value = '', $disabled = false){
    if($name == '')
      $this->html .= $this->error("Input without name ");
    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';
      
    $this->html .= '<script src="'.BASE_URL.MODULE_DIR.'developer/std_mod/calendar/datetimepicker_css.js"></script>' ;
    $this->html .= '<script src="'.BASE_URL.MODULE_DIR.'developer/std_mod/calendar/datetimepicker_css.js"></script>' ;
    $this->html .= '<input id="'.htmlspecialchars($name).'" autocomplete="off" '.$disabledStr.' class="stdMod" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'"/>
    <a href="javascript: NewCssCal(\''.htmlspecialchars($name).'\',\'yyyymmdd\',\'dropdown\',false,24,false)"><img src="'.BASE_URL.MODULE_DIR.'developer/std_mod/calendar/images/cal.gif"" border="0"/></a>' ;
  }  
  
  function textarea($name, $value = '', $disabled = false){
    if($name == '')
      $this->html .= $this->error("Input without name ");
    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';
      
      
    $this->html .= '<textarea cols="50" rows="10" '.$disabledStr.' class="stdMod" name="'.htmlspecialchars($name).'" >'.$value.'</textarea>' ;
  }

  function inputFile($name, $disabled = false){
    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';

    if($name == '')
      $this->html .= $this->error("Input without name ");
    $this->html .= '<input '.$disabledStr.'  type="file" class="stdModFile" name="'.htmlspecialchars($name).'" />' ;
  }
  
  function inputCheckbox($name, $checked, $disabled = false){
    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';

    if($name == '')
      $this->html .= $this->error("Input without name ");
    if($checked)
      $this->html .= '<input '.$disabledStr.' checked type="checkbox" class="stdModBox" name="'.htmlspecialchars($name).'" />' ;
    else
      $this->html .= '<input '.$disabledStr.' type="checkbox" class="stdModBox" name="'.htmlspecialchars($name).'" />' ;
  }
  
  function inputPassword($name, $value='', $value2='', $disabled = false){
    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';

    if($name == '')
        $this->html .= $this->error("Input without name ");

    $this->html .=     
    '
             <input autocomplete="off" '.$disabledStr.' type="password" name="'.$name.'" value="'.htmlspecialchars($value).'"/><br />
             <input autocomplete="off" '.$disabledStr.' type="password" name="'.$name.'_confirm" value="'.htmlspecialchars($value2).'"/>
       
    ';      
        

  }
    
  //eof inputs
  
  
  function inputSelect($name, $values, $currentValue, $disabled = false){

    if($disabled)
      $disabledStr = ' disabled ';
    else
      $disabledStr = ' ';    
    
    
    $answer = '<select '.$disabledStr.' class="stdMod" name="'.htmlspecialchars($name).'" >';
    
    $valueExists = false;    
    foreach($values as $key => $value){
      if ($value[0] == $currentValue){
        $selected = " selected ";
        $valueExists = true;
      }else{
        $selected = "";
      } 
      $answer .= '<option value="'.htmlspecialchars($value[0]).'" '.$selected.'>'.htmlspecialchars($value[1]).'</option>';
    }
    
    
    if(!$valueExists){
      $answer .= '<option value="'.htmlspecialchars($currentValue).'" selected>'.htmlspecialchars($currentValue).' (not in list)</option>';
    }
    
    $answer .= '</select>';  
    
    $this->html .= $answer;
  }
  
  function error($error){
   /* $this->html .= '
      <script>
        alert("'.$error.'");
      </script>
    ';*/
    $this->html .= '
      <p>'.$error.'</p>';    
    
  }
  

  function html($code){
    $this->html .= $code;
  }

  function send(){
    echo $this->html;
  }

}

