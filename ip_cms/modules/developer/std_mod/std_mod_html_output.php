<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2009 JSC Apro media.
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
    $this->html .= '<b>'.$title.'</b><br/>';
  
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
          tinyMCE.init({          
            theme : "advanced",
            mode: "specific_textareas",
            editor_selector : "mceEditor",
            plugins : "paste,simplebrowser,advlink,advimage,inlinepopups",
            theme_advanced_buttons1 : "cut,copy,pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
            theme_advanced_buttons2 : "bold,italic,underline,styleselect,image",
            theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup,code",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_resizing : true,            
            theme_advanced_path_location : "bottom",
            extended_valid_elements : "style",
            content_css : "'.BASE_URL.THEME_DIR.THEME.'/default_content.css",
            theme_advanced_styles : "'.$tinyMceStylesStr.'",

           
            file_browser_callback : "simplebrowser_browse", // This is required
            
            
            forced_root_block : "p",
            width: "500px",
            height: "400px",
            
            document_base_url : "'.BASE_URL.'",
            remove_script_host : true,
            relative_urls : false,
            convert_urls : false,        
            
            
            paste_auto_cleanup_on_paste : true,
            paste_retain_style_properties: false,
            paste_strip_class_attributes: true,
            paste_remove_spans: true,
            paste_remove_styles: true,
            paste_convert_middot_lists: true,
            
            paste_preprocess : function(pl, o) {
              o.content = o.content.stripScripts(); 
              var tmpContent = o.content;
              
              tmpContent = ip_paste_preprocess_function(tmpContent);
              
              o.content = tmpContent;
            },
            paste_postprocess : function(pl, o) {
                // Content DOM node containing the DOM structure of the clipboard
                //alert(o.node.innerHTML);
            }             
            
          });
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

