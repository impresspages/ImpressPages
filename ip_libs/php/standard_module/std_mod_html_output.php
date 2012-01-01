<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Library\Php\StandardModule;

if (!defined('BACKEND')) exit;

class std_mod_html_output{
    var $html;
    var $tabs_id;
    var $tab_id;
    var $tabs_tmp_html;
    var $tab_titles;
    var $tab_contents;
    var $tiny_mce_fields;

    function __construct(){
        global $cms;
        $this->tiny_mce_fields = array();
        $cms->tiny_mce = false;
    }

    function html_output(){
        $this->html = '';
    }


    function field_name($title){
        $this->html .= '<b>'.$title.'</b><br/>';

    }

    //tabs
    function tabs_open(){
        $this->tabs_tmp_html = $this->html;
        $this->html = '';
        $this->tabs_id = rand(0, 1000000);
        $this->tab_id = 0;

        $this->tab_titles = array();
        $this->tab_contents = array();

    }

    function tabs_close(){
        $this->html = $this->tabs_tmp_html.'
    <script type="text/javascript">
      var stdMod'.$this->tabs_id.' = new LibTabs(\'stdMod'.$this->tabs_id.'\', \'modTabs\', \'modTabs2\');      
    </script>
    ';

        $this->html .= '<div class="stdModTabs">';
        foreach($this->tab_titles as $key => $title){
            $this->html .= '<a id="stdModLink'.$this->tabs_id.'At'.$key.'">'.htmlspecialchars($title).'</a> ';
        }
        $this->html .= '</div>';

        foreach($this->tab_contents as $key => $content){
            $this->html .= '<div id="stdModCont'.$this->tabs_id.'At'.$key.'">'.$content.'</div>';
            $this->html .= '
      <script type="text/javascript">
        stdMod'.$this->tabs_id.'.addTab(\'stdModLink'.$this->tabs_id.'At'.$key.'\', \'stdModCont'.$this->tabs_id.'At'.$key.'\');      
      </script>
      ';
        }

        $this->html .= '
    <script type="text/javascript">
      stdMod'.$this->tabs_id.'.switchFirst();      
    </script>';


    }

    function tab_open($title){
        $this->tab_titles[$this->tab_id] = $title;
    }

    function tab_close(){
        $this->tab_contents[$this->tab_id] = $this->html;
        $this->tab_id++;
        $this->html = '';
    }

    //eof tabs

    function label($label){
        $this->html .= '<span class="label">'.$label.'</span>';
    }

    //inputs

    function wysiwyg($name, $value = '', $disabled = false){
        global $site;

        if($disabled) {
            $disabled_str = ' disabled ';
        } else {
            $disabled_str = ' ';
        }

        if($name == '') {
            $this->html .= $this->error("Input without name ");
        }


        $this->html .= '<textarea class="mceEditor" mce_editable="true" '.$disabled_str.' class="stdMod" cols="100" rows="10" name="'.htmlspecialchars($name).'">'.$value.'</textarea>' ;
        
        
   
    }

    function input($name, $value = '', $disabled = false, $max_length = null){
        if($name == '')
        $this->html .= $this->error("Input without name ");
        if($disabled)
        $disabled_str = ' disabled ';
        else
        $disabled_str = ' ';

        if($max_length)
        $max_length_str = ' maxlength="'.$max_length.'" ';
        else
        $max_length_str = '';

        $this->html .= '<input autocomplete="off" '.$max_length_str.'  '.$disabled_str.' class="stdMod" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'"/>' ;
    }

    function textarea($name, $value = '', $disabled = false){
        if($name == '')
        $this->html .= $this->error("Input without name ");
        if($disabled)
        $disabled_str = ' disabled ';
        else
        $disabled_str = ' ';


        $this->html .= '<textarea cols="50" rows="10" '.$disabled_str.' class="stdMod" name="'.htmlspecialchars($name).'" >'.$value.'</textarea>' ;
    }

    function input_file($name, $disabled = false){
        if($disabled)
        $disabled_str = ' disabled ';
        else
        $disabled_str = ' ';

        if($name == '')
        $this->html .= $this->error("Input without name ");
        $this->html .= '<input '.$disabled_str.'  type="file" class="stdModFile" name="'.htmlspecialchars($name).'" />' ;
    }

    function input_checkbox($name, $checked, $disabled = false){
        if($disabled)
        $disabled_str = ' disabled ';
        else
        $disabled_str = ' ';

        if($name == '')
        $this->html .= $this->error("Input without name ");
        if($checked)
        $this->html .= '<input '.$disabled_str.' checked type="checkbox" class="stdModBox" name="'.htmlspecialchars($name).'" />' ;
        else
        $this->html .= '<input '.$disabled_str.' type="checkbox" class="stdModBox" name="'.htmlspecialchars($name).'" />' ;
    }

    function input_password($name, $value='', $value2='', $disabled = false){
        if($disabled)
        $disabled_str = ' disabled ';
        else
        $disabled_str = ' ';

        if($name == '')
        $this->html .= $this->error("Input without name ");

        $this->html .=
    '
             <input autocomplete="off" '.$disabled_str.' type="password" name="'.$name.'" value="'.htmlspecialchars($value).'"/><br />
             <input autocomplete="off" '.$disabled_str.' type="password" name="'.$name.'_confirm" value="'.htmlspecialchars($value2).'"/>
       
    ';      

        /*    $this->html .=
         '
         <table>
         <tr>
         <td>
         <input autocomplete="off" '.$disabled_str.' type="password" name="'.$name.'" value="'.htmlspecialchars($value).'"/>
         </td>
         <td>
         <input autocomplete="off" '.$disabled_str.' type="password" name="'.$name.'_confirm" value="'.htmlspecialchars($value2).'"/>
         </td>
         </tr>
         </table>
         ';*/
    }

    //eof inputs

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

