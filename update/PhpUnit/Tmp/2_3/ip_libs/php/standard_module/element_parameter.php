<?php
/**
 * @package		Library
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Library\Php\StandardModule;

if (!defined('BACKEND')) exit;

class element_parameter extends Element{ //data element in area
    var $default_value;
    function print_field_new($prefix, $parent_id = null, $area = null){
        global $parametersMod;
        global $std_mod_db;
        $html = new std_mod_html_output();
        $html->html('

			
        <select id="std_mod_parameter_type_'.$prefix.'" onchange="std_mod_parameter_type_change_'.$prefix.'()" name="'.$prefix.'">
          <option value="string">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_string')).'</option>
          <option value="integer">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_integer')).'</option>
          <option value="bool">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_bool')).'</option>
          <option value="textarea">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_textarea')).'</option>
          <option value="string_wysiwyg">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_wysiwyg')).'</option>
          <option value="lang">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_string_lang')).'</option>
          <option value="lang_textarea">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_textarea_lang')).'</option>
          <option value="lang_wysiwyg">'.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_wysiwyg_lang')).'</option>
         <!-- <option value="photo">Nuotrauka</option>-->
        </select>    <br /><br />
      ');

        //string
        $html->html('<div id="'.$prefix.'_div_string" style="display: none;">');
        $html->input($prefix.'_string', '');
        $html->html('</div>');
        //number
        $html->html('<div id="'.$prefix.'_div_integer" style="display: none;">');
        $html->input($prefix.'_integer', '');
        $html->html('</div>');
        //bool
        $html->html('<div id="'.$prefix.'_div_bool" style="display: none;">');
        $html->input_checkbox($prefix.'_bool', '');
        $html->html('</div>');
        //textarea
        $html->html('<div id="'.$prefix.'_div_textarea" style="display: none;">');
        $html->textarea($prefix.'_textarea', '');
        $html->html('</div>');
        //wysiwyg
        $html->html('<div id="'.$prefix.'_div_string_wysiwyg" style="display: none;">');
        $html->wysiwyg($prefix.'_wysiwyg', '');
        $html->html('</div>');

        //string lang
        $languages = $std_mod_db->languages();

        $html->html('<div id="'.$prefix.'_div_lang" style="display: none;">');
        foreach($languages as $key => $language){
            $html->html('<span class="label">'.$language['d_short'].'</span><br />');
            $html->input($prefix.'_lang_'.$language['id'], '');
            $html->html("<br />");
        }
        $html->html('</div>');

        //textarea lang

        $languages = $std_mod_db->languages();
        $html->html('<div id="'.$prefix.'_div_lang_textarea" style="display: none;">');
        foreach($languages as $key => $language){
            $html->html('<span class="label">'.$language['d_short'].'</span><br />');
            $html->textarea($prefix.'_lang_textarea_'.$language['id'], '');
            $html->html('<br/>');
        }
        $html->html('</div>');

        //wysiwyg lang

        $languages = $std_mod_db->languages();
        $html->html('<div id="'.$prefix.'_div_lang_wysiwyg" style="display: none;">');
        foreach($languages as $key => $language){
            $html->html('<span class="label">'.$language['d_short'].'</span>');
            $html->wysiwyg($prefix.'_lang_wysiwyg_'.$language['id'], '');
        }
        $html->html('</div>');



        $html->html('
	
		<script type="text/javascript">
			function std_mod_parameter_type_change_'.$prefix.'(){
				document.getElementById(\''.$prefix.'_div_string\').style.display = \'none\';
				document.getElementById(\''.$prefix.'_div_integer\').style.display = \'none\';
				document.getElementById(\''.$prefix.'_div_bool\').style.display = \'none\';
				document.getElementById(\''.$prefix.'_div_textarea\').style.display = \'none\';
				document.getElementById(\''.$prefix.'_div_string_wysiwyg\').style.display = \'none\';
				document.getElementById(\''.$prefix.'_div_lang\').style.display = \'none\';
				document.getElementById(\''.$prefix.'_div_lang_textarea\').style.display = \'none\';
				document.getElementById(\''.$prefix.'_div_lang_wysiwyg\').style.display = \'none\';
				
				document.getElementById(\''.$prefix.'_div_\' + document.getElementById(\'std_mod_parameter_type_'.$prefix.'\').value).style.display = \'inline\';
			}
			
			std_mod_parameter_type_change_'.$prefix.'();
		</script>	
	');


         
        return $html->html;
    }


    function print_field_update($prefix, $parent_id = null, $area = null){
        $html = new std_mod_html_output();
        global $std_mod_db;
        $value = $this->default_value;
        $sql = "select * from `".DB_PREF."".$area->get_db_table()."` where ".$area->get_db_key()." = '".$parent_id."' ";
        $rs = mysql_query($sql);
        if (!$rs)
        trigger_error("Can not get text field data. ".$sql." ".mysql_error());
        if ($lock = mysql_fetch_assoc($rs)){

            if($lock['type'] == 'string_wysiwyg'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get text field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $value = $lock2['value'];
                    else
                    trigger_error("Can not get text field data. ".$sql);
                }
                $html->wysiwyg($prefix.'_string', $value);
            }
            if($lock['type'] == 'string'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get text field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $value = $lock2['value'];
                    else
                    trigger_error("Can not get text field data. ".$sql);
                }
                $html->input($prefix.'_string', $value);
            }
             
            if($lock['type'] == 'integer'){
                $sql = "select value from `".DB_PREF."par_integer` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get integer field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $value = $lock2['value'];
                    else
                    trigger_error("Can not get integer field data. ".$sql);
                }
                $html->input($prefix.'_integer', $value);
            }


            if($lock['type'] == 'bool'){
                $sql = "select value from `".DB_PREF."par_bool` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get bool field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $value = $lock2['value'];
                    else
                    trigger_error("Can not get bool field data. ".$sql);
                }
                $html->input_checkbox($prefix.'_bool', $value);
            }
             
            if($lock['type'] == 'textarea'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get text field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $value = $lock2['value'];
                    else
                    trigger_error("Can not get text field data. ".$sql);
                }
                $html->textarea($prefix.'_string', $value);
            }
            if($lock['type'] == 'lang'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = mysql_query($sql2);
                if (!$rs2)
                trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
                else{
                    $values = array();
                    while($lock2 = mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = $std_mod_db->languages();

                    $answer .= '';
                    foreach($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = mysql_query($sql3);
                        $value='';
                        if($rs3){
                            if($lock3 = mysql_fetch_assoc($rs3))
                            $value = $lock3['translation'];
                        }else trigger_error("Can't get all languages ".$sql3." ".mysql_error());
                        $html->html('<span class="label">'.$language['d_short'].'</span><br />');
                        $html->input($prefix.'_'.$language['id'], $value);
                        $html->html("<br />");
                    }

                }
            }

            if($lock['type'] == 'lang_textarea'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = mysql_query($sql2);
                if (!$rs2)
                trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
                else{
                    $values = array();
                    while($lock2 = mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = $std_mod_db->languages();


                     
                     
                    $answer .= '';
                    foreach($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = mysql_query($sql3);
                        $value='';
                        if($rs3){
                            if($lock3 = mysql_fetch_assoc($rs3))
                            $value = $lock3['translation'];
                        }else trigger_error("Can't get all languages ".$sql3." ".mysql_error());
                        $html->html('<span class="label">'.$language['d_short'].'</span><br />');
                        //	$html->html('<div class="label">');
                        $html->textarea($prefix.'_'.$language['id'], $value);
                        $html->html("<br />");
                        //$html->html('</div>');
                    }


                }
            }

             
            if($lock['type'] == 'lang_wysiwyg'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = mysql_query($sql2);
                if (!$rs2)
                trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
                else{
                    $values = array();
                    while($lock2 = mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = $std_mod_db->languages();


                     
                     
                    $answer .= '';
                    foreach($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = mysql_query($sql3);
                        $value='';
                        if($rs3){
                            if($lock3 = mysql_fetch_assoc($rs3))
                            $value = $lock3['translation'];
                        }else trigger_error("Can't get all languages ".$sql3." ".mysql_error());
                        $html->html('<span class="label">'.$language['d_short'].'</span><br />');
                        $html->html('<div class="label">');
                        $html->wysiwyg($prefix.'_'.$language['id'], $value);
                        $html->html('</div>');
                    }


                }
            }

        }

        $html->html('<input type="hidden" name="'.$prefix.'" value="'.$lock['type'].'" //>');

        return $html->html;
    }



    function preview_value($value, $parent_id = null, $area = null){
        global $std_mod_db;

        $sql = "select * from `".DB_PREF."".$area->get_db_table()."` where ".$area->get_db_key()." = '".$value."' ";
        $rs = mysql_query($sql);
        if (!$rs)
        trigger_error("Can not get text field data. ".$sql." ".mysql_error());
        if ($lock = mysql_fetch_assoc($rs)){

            if($lock['type'] == 'string_wysiwyg'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get text field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $answer = mb_substr($lock2['value'], 0, 25);
                    else
                    trigger_error("Can not get text field data. ".$sql);
                }
            }
            if($lock['type'] == 'string'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get text field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $answer = $lock2['value'];
                    else
                    trigger_error("Can not get text field data. ".$sql);
                }
            }

            if($lock['type'] == 'integer'){
                $sql = "select value from `".DB_PREF."par_integer` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get integer field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $answer = $lock2['value'];
                    else
                    trigger_error("Can not get integer field data. ".$sql);
                }
            }

            if($lock['type'] == 'bool'){
                $sql = "select value from `".DB_PREF."par_bool` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get bool field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs)){
                        if($lock2['value'])
                        $answer = '+';
                        else
                        $answer = '-';
                    }else
                    trigger_error("Can not get bool field data. ".$sql);
                }
            }

            if($lock['type'] == 'textarea'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = mysql_query($sql);
                if (!$rs)
                trigger_error("Can not get textarea field data. ".$sql." ".mysql_error());
                else{
                    if($lock2 = mysql_fetch_assoc($rs))
                    $answer = $lock2['value'];
                    else
                    trigger_error("Can not get textarea field data. ".$sql);
                }
            }

            if($lock['type'] == 'lang'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = mysql_query($sql2);
                if (!$rs2)
                trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
                else{
                    $values = array();
                    while($lock2 = mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = $std_mod_db->languages();

                    foreach($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = mysql_query($sql3);
                        $value='';
                        if($rs3){
                            if($lock3 = mysql_fetch_assoc($rs3))
                            $answer .= '/'.$lock3['translation'];
                        }else trigger_error("Can't get all languages ".$sql3." ".mysql_error());
                    }


                }
            }

            if($lock['type'] == 'lang_textarea'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = mysql_query($sql2);
                if (!$rs2)
                trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
                else{
                    $values = array();
                    while($lock2 = mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = $std_mod_db->languages();

                    foreach($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = mysql_query($sql3);
                        $value='';
                        if($rs3){
                            if($lock3 = mysql_fetch_assoc($rs3))
                            $answer .= '/'.substr($lock3['translation'], 0, 20);
                        }else trigger_error("Can't get all languages ".$sql3." ".mysql_error());
                    }


                }
            }

            if($lock['type'] == 'lang_wysiwyg'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = mysql_query($sql2);
                if (!$rs2)
                trigger_error("Can not get language field data. ".$sql2." ".mysql_error());
                else{
                    $values = array();
                    while($lock2 = mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = $std_mod_db->languages();

                    foreach($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = mysql_query($sql3);
                        $value='';
                        if($rs3){
                            if($lock3 = mysql_fetch_assoc($rs3))
                            $answer .= '/'.substr($lock3['translation'], 0, 20);
                        }else trigger_error("Can't get all languages ".$sql3." ".mysql_error());
                    }


                }
            }

        }


        return htmlspecialchars($answer);
    }

    function check_field($prefix, $action){
        return null;


    }

    function get_parameters($action, $prefix){
        if($this->read_only)
        return;
        else
        return array("name"=>"type", "value"=>$_REQUEST[''.$prefix]);
    }


    function process_insert($prefix, $area, $last_insert_id){
        global $std_mod_db;
        switch($_REQUEST[''.$prefix]){
            case "string_wysiwyg":
                $sql = "insert into `".DB_PREF."par_string` set value = '".mysql_real_escape_string($_REQUEST[$prefix.'_wysiwyg'])."', parameter_id = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                break;
            case "string":
                $sql = "insert into `".DB_PREF."par_string` set value = '".mysql_real_escape_string($_REQUEST[$prefix.'_string'])."', parameter_id = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                break;
            case "integer":
                if($_REQUEST[$prefix.'_integer'] == '')
                $value = ' NULL ';
                else
                $value = " '".mysql_real_escape_string($_REQUEST[$prefix.'_integer'])."' ";

                $sql = "insert into `".DB_PREF."par_integer` set value = ".$value.", parameter_id = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                break;
            case "bool":
                if(isset($_REQUEST[$prefix.'_bool']))
                $value = ' 1 ';
                else
                $value = " 0 ";

                $sql = "insert into `".DB_PREF."par_bool` set value = ".$value.", parameter_id = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                break;
            case "textarea":
                $sql = "insert into `".DB_PREF."par_string` set value = '".mysql_real_escape_string(str_replace("\r", '', $_REQUEST[$prefix.'_textarea']))."', parameter_id = ".$last_insert_id."";
                $rs = mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".mysql_error());
                break;

            case "lang":
                $languages = $std_mod_db->languages();
                foreach($languages as $key => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".mysql_real_escape_string($_REQUEST[$prefix.'_lang_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = ".$last_insert_id." ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                }
                break;
            case "lang_textarea":
                $languages = $std_mod_db->languages();
                foreach($languages as $key => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".mysql_real_escape_string($_REQUEST[$prefix.'_lang_textarea_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = ".$last_insert_id." ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                }
                break;
            case "lang_wysiwyg":
                $languages = $std_mod_db->languages();
                foreach($languages as $key => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".mysql_real_escape_string($_REQUEST[$prefix.'_lang_wysiwyg_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = ".$last_insert_id." ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                }
                break;
        }
    }
    function process_update($prefix, $area, $key){
        global $std_mod_db;


        if($_REQUEST[''.$prefix] == 'string_wysiwyg'){
            $sql = "update `".DB_PREF."par_string` set value='".mysql_real_escape_string($_REQUEST[''.$prefix.'_string'])."' where parameter_id = '".$key."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".mysql_error());
        }
        if($_REQUEST[''.$prefix] == 'string'){
            $sql = "update `".DB_PREF."par_string` set value='".mysql_real_escape_string($_REQUEST[''.$prefix.'_string'])."' where parameter_id = '".$key."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".mysql_error());
        }

        if($_REQUEST[''.$prefix] == 'integer'){
            if($_REQUEST[''.$prefix.'_integer'] != '')
            $sql = "update `".DB_PREF."par_integer` set value='".mysql_real_escape_string($_REQUEST[''.$prefix.'_integer'])."' where parameter_id = '".$key."' ";
            else
            $sql = "update `".DB_PREF."par_integer` set value=NULL where parameter_id = '".$key."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".mysql_error());
        }

        if($_REQUEST[''.$prefix] == 'bool'){
            if(isset($_REQUEST[$prefix.'_bool']))
            $value = ' 1 ';
            else
            $value = " 0 ";

            $sql = "update `".DB_PREF."par_bool` set value=".$value." where parameter_id = '".$key."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".mysql_error());
        }

        if($_REQUEST[''.$prefix] == 'textarea'){
            $sql = "update `".DB_PREF."par_string` set value='".mysql_real_escape_string(str_replace("\r", "", $_REQUEST[''.$prefix.'_string']))."' where parameter_id = '".$key."' ";
            $rs = mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".mysql_error());
        }


        if($_REQUEST[''.$prefix] == 'lang'){
            $languages = $std_mod_db->languages();
             
            $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$key."' ";
            $rs2 = mysql_query($sql2);
            if($rs2){
                foreach($languages as $key2 => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', language_id = '".$language['id']."',  parameter_id = ".$key." ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                }
            }else
            trigger_error("Can't update parameter ".$sql2." ".mysql_error());
             
             
             
        }

        if($_REQUEST[''.$prefix] == 'lang_textarea'){
            $languages = $std_mod_db->languages();
             
            $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$key."' ";
            $rs2 = mysql_query($sql2);
            if($rs2){
                foreach($languages as $key2 => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = '".$key."' ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                }
            }else
            trigger_error("Can't update parameter ".$sql2." ".mysql_error());
        }

        if($_REQUEST[''.$prefix] == 'lang_wysiwyg'){
            $languages = $std_mod_db->languages();
             
            $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$key."' ";
            $rs2 = mysql_query($sql2);
            if($rs2){
                foreach($languages as $key2 => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = '".$key."' ";
                    $rs3 = mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".mysql_error());
                }
            }else
            trigger_error("Can't update parameter ".$sql2." ".mysql_error());
        }


    }
    function process_delete($area, $key){
        global $std_mod_db;
        $sql = "select * from `".DB_PREF."".$area->get_db_table()."` where ".$area->get_db_key()." = '".$key."' ";
        $rs = mysql_query($sql);
        if($rs){
            if($lock = mysql_fetch_assoc($rs)){

                if($lock['type'] == 'string_wysiwyg'){
                    $sql = "delete from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".mysql_error());
                }
                if($lock['type'] == 'string'){
                    $sql = "delete from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".mysql_error());
                }


                if($lock['type'] == 'textarea'){
                    $sql = "delete from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".mysql_error());
                }
                if($lock['type'] == 'bool'){
                    $sql = "delete from `".DB_PREF."par_bool` where parameter_id = '".$lock['id']."' ";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".mysql_error());
                }

                if($lock['type'] == 'integer'){
                    $sql = "delete from `".DB_PREF."par_integer` where parameter_id = '".$lock['id']."' ";
                    $rs = mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".mysql_error());
                }
                if($lock['type'] == 'lang'){
                    $languages = $std_mod_db->languages();

                    $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$lock['id']."' ";
                    $rs2 = mysql_query($sql2);
                    if(!$rs2)
                    trigger_error("Can't delete parameter ".$sql2." ".mysql_error());

                }

                if($lock['type'] == 'lang_textarea'){
                    $languages = $std_mod_db->languages();

                    $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$lock['id']."' ";
                    $rs2 = mysql_query($sql2);
                    if(!$rs2)
                    trigger_error("Can't delete parameter ".$sql2." ".mysql_error());

                }

                if($lock['type'] == 'lang_wysiwyg'){
                    $languages = $std_mod_db->languages();

                    $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$lock['id']."' ";
                    $rs2 = mysql_query($sql2);
                    if(!$rs2)
                    trigger_error("Can't delete parameter ".$sql2." ".mysql_error());

                }

            } else trigger_error("Can't delete parameter ".$sql);
        }else trigger_error("Can't delete parameter ".$sql." ".mysql_error());

    }

    function print_search_field($level, $key){
    }

    function get_filter_option($value){
    }

    function set_default_value($default_value){
        $this->default_value = $default_value;
    }
    function get_default_value(){
        return $this->default_value;
    }






}

