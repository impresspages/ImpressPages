<?php
/**
 * @package		Library
 *
 *
 */
namespace Ip\Lib\StdMod\Element;


class Parameter extends Element{ //data element in area
    var $default_value;



    function printFieldNew($prefix, $parent_id = null, $area = null){
        global $parametersMod;
        global $std_mod_db;
        $html = new \Ip\Lib\StdMod\StdModHtmlOutput();
        $html->html('

			
        <select id="std_mod_parameter_type_'.$prefix.'" onchange="std_mod_parameter_type_change_'.$prefix.'()" name="'.$prefix.'">
          <option value="string">'.htmlspecialchars(__('String', 'ipAdmin')).'</option>
          <option value="integer">'.htmlspecialchars(__('Integer', 'ipAdmin')).'</option>
          <option value="bool">'.htmlspecialchars(__('Boolean', 'ipAdmin')).'</option>
          <option value="textarea">'.htmlspecialchars(__('Textarea', 'ipAdmin')).'</option>
          <option value="string_wysiwyg">'.htmlspecialchars(__('Wysiwyg', 'ipAdmin')).'</option>
          <option value="lang">'.htmlspecialchars(__('String languages', 'ipAdmin')).'</option>
          <option value="lang_textarea">'.htmlspecialchars(__('Textarea languages', 'ipAdmin')).'</option>
          <option value="lang_wysiwyg">'.htmlspecialchars(__('Wysiwyg languages', 'ipAdmin')).'</option>
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
        $html->inputCheckbox($prefix.'_bool', '');
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
        $languages = \Ip\Lib\StdMod\StdModDb::languages();

        $html->html('<div id="'.$prefix.'_div_lang" style="display: none;">');
        foreach($languages as $key => $language){
            $html->html('<span class="label">'.$language['d_short'].'</span><br />');
            $html->input($prefix.'_lang_'.$language['id'], '');
            $html->html("<br />");
        }
        $html->html('</div>');

        //textarea lang

        $languages = \Ip\Lib\StdMod\StdModDb::languages();
        $html->html('<div id="'.$prefix.'_div_lang_textarea" style="display: none;">');
        foreach($languages as $key => $language){
            $html->html('<span class="label">'.$language['d_short'].'</span><br />');
            $html->textarea($prefix.'_lang_textarea_'.$language['id'], '');
            $html->html('<br/>');
        }
        $html->html('</div>');

        //wysiwyg lang

        $languages = \Ip\Lib\StdMod\StdModDb::languages();
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


    function printFieldUpdate($prefix, $record, $area){
        $parent_id = $record[$area->dbReference];
        $html = new \Ip\Lib\StdMod\StdModHtmlOutput();
        global $std_mod_db;
        $value = $this->default_value;
        $sql = "select * from `".DB_PREF."".$area->dbTable."` where ".$area->dbPrimaryKey." = '".$parent_id."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs) {
            trigger_error("Can not get text field data. ".$sql." ".ip_deprecated_mysql_error());
        }

        if (!$lock = ip_deprecated_mysql_fetch_assoc($rs)){
            throw new \Ip\Exception('Empty query result ' . $sql);
        }
        if($lock['type'] == 'string_wysiwyg'){
            $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if (!$rs) {
                trigger_error("Can not get text field data. ".$sql." ".ip_deprecated_mysql_error());
            } else {
                if($lock2 = ip_deprecated_mysql_fetch_assoc($rs))
                $value = $lock2['value'];
                else
                trigger_error("Can not get text field data. ".$sql);
            }
            $html->wysiwyg($prefix.'_string', $value);
        }
        if($lock['type'] == 'string'){
            $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if (!$rs) {
                trigger_error("Can not get text field data. ".$sql." ".ip_deprecated_mysql_error());
            } else {
                if($lock2 = ip_deprecated_mysql_fetch_assoc($rs))
                $value = $lock2['value'];
                else
                trigger_error("Can not get text field data. ".$sql);
            }
            $html->input($prefix.'_string', $value);
        }

        if($lock['type'] == 'integer'){
            $sql = "select value from `".DB_PREF."par_integer` where parameter_id = '".$lock['id']."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if (!$rs) {
                trigger_error("Can not get integer field data. ".$sql." ".ip_deprecated_mysql_error());
            } else {
                if($lock2 = ip_deprecated_mysql_fetch_assoc($rs))
                $value = $lock2['value'];
                else
                trigger_error("Can not get integer field data. ".$sql);
            }
            $html->input($prefix.'_integer', $value);
        }


        if($lock['type'] == 'bool'){
            $sql = "select value from `".DB_PREF."par_bool` where parameter_id = '".$lock['id']."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if (!$rs) {
                trigger_error("Can not get bool field data. ".$sql." ".ip_deprecated_mysql_error());
            } else {
                if($lock2 = ip_deprecated_mysql_fetch_assoc($rs)) {
                    $value = $lock2['value'];
                } else {
                    trigger_error("Can not get bool field data. ".$sql);
                }
            }
            $html->input_checkbox($prefix.'_bool', $value);
        }

        if($lock['type'] == 'textarea'){
            $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if (!$rs) {
                trigger_error("Can not get text field data. ".$sql." ".ip_deprecated_mysql_error());
            } else {
                if($lock2 = ip_deprecated_mysql_fetch_assoc($rs))
                $value = $lock2['value'];
                else
                trigger_error("Can not get text field data. ".$sql);
            }
            $html->textarea($prefix.'_string', $value);
        }
        if($lock['type'] == 'lang'){
            $answer = '';
            $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if (!$rs2) {
                trigger_error("Can not get language field data. ".$sql2." ".ip_deprecated_mysql_error());
            } else{
                $values = array();
                while($lock2 = ip_deprecated_mysql_fetch_assoc($rs2)){
                    $values[$lock2['l_id']] = $lock2['translation'];
                }

                $languages = \Ip\Lib\StdMod\StdModDb::languages();

                $answer .= '';
                foreach($languages as $key => $language){
                    $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    $value='';
                    if($rs3){
                        if($lock3 = ip_deprecated_mysql_fetch_assoc($rs3))
                        $value = $lock3['translation'];
                    }else trigger_error("Can't get all languages ".$sql3." ".ip_deprecated_mysql_error());
                    $html->html('<span class="label">'.$language['d_short'].'</span><br />');
                    $html->input($prefix.'_'.$language['id'], $value);
                    $html->html("<br />");
                }

            }
        }

        if($lock['type'] == 'lang_textarea'){
            $answer = '';
            $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if (!$rs2) {
                trigger_error("Can not get language field data. ".$sql2." ".ip_deprecated_mysql_error());
            } else {
                $values = array();
                while($lock2 = ip_deprecated_mysql_fetch_assoc($rs2)){
                    $values[$lock2['l_id']] = $lock2['translation'];
                }

                $languages = \Ip\Lib\StdMod\StdModDb::languages();




                $answer .= '';
                foreach($languages as $key => $language){
                    $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    $value='';
                    if($rs3){
                        if($lock3 = ip_deprecated_mysql_fetch_assoc($rs3)) {
                            $value = $lock3['translation'];
                        }
                    } else {
                        trigger_error("Can't get all languages ".$sql3." ".ip_deprecated_mysql_error());
                    }
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
            $rs2 = ip_deprecated_mysql_query($sql2);
            if (!$rs2) {
                trigger_error("Can not get language field data. ".$sql2." ".ip_deprecated_mysql_error());
            } else {
                $values = array();
                while($lock2 = ip_deprecated_mysql_fetch_assoc($rs2)){
                    $values[$lock2['l_id']] = $lock2['translation'];
                }

                $languages = \Ip\Lib\StdMod\StdModDb::languages();




                $answer .= '';
                foreach($languages as $key => $language){
                    $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    $value='';
                    if($rs3){
                        if($lock3 = ip_deprecated_mysql_fetch_assoc($rs3)) {
                            $value = $lock3['translation'];
                        }
                    } else {
                        trigger_error("Can't get all languages ".$sql3." ".ip_deprecated_mysql_error());
                    }
                    $html->html('<span class="label">'.$language['d_short'].'</span><br />');
                    $html->html('<div class="label">');
                    $html->wysiwyg($prefix.'_'.$language['id'], $value);
                    $html->html('</div>');
                }


            }
        }

        $html->html('<input type="hidden" name="'.$prefix.'" value="'.$lock['type'].'" />');

        return $html->html;
    }


    /**
     * @param $record
     * @param $area \Ip\Lib\StdMod\Area
     * @return string
     */
    public function previewValue($record, $area){

        $value = $record[$area->dbPrimaryKey];


        $sql = "select * from `".DB_PREF."".$area->dbTable."` where ".$area->dbPrimaryKey." = '".$value."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if (!$rs) {
            trigger_error("Can not get text field data. ".$sql." ".ip_deprecated_mysql_error());
        }
        if ($lock = ip_deprecated_mysql_fetch_assoc($rs)){

            if($lock['type'] == 'string_wysiwyg'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if (!$rs) {
                    trigger_error("Can not get text field data. ".$sql." ".ip_deprecated_mysql_error());
                } else {
                    if($lock2 = ip_deprecated_mysql_fetch_assoc($rs)) {
                        $answer = mb_substr($lock2['value'], 0, 25);
                    } else {
                        trigger_error("Can not get text field data. ".$sql);
                    }
                }
            }
            if($lock['type'] == 'string'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if (!$rs) {
                    trigger_error("Can not get text field data. ".$sql." ".ip_deprecated_mysql_error());
                } else {
                    if($lock2 = ip_deprecated_mysql_fetch_assoc($rs)) {
                        $answer = $lock2['value'];
                    } else {
                        $answer = '';
                    }
                }
            }

            if($lock['type'] == 'integer'){
                $sql = "select value from `".DB_PREF."par_integer` where parameter_id = '".$lock['id']."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if (!$rs) {
                    trigger_error("Can not get integer field data. ".$sql." ".ip_deprecated_mysql_error());
                } else {
                    if ($lock2 = ip_deprecated_mysql_fetch_assoc($rs)) {
                        $answer = $lock2['value'];
                    } else {
                        trigger_error("Can not get integer field data. ".$sql);
                    }
                }
            }

            if($lock['type'] == 'bool'){
                $sql = "select value from `".DB_PREF."par_bool` where parameter_id = '".$lock['id']."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if (!$rs) {
                    trigger_error("Can not get bool field data. ".$sql." ".ip_deprecated_mysql_error());
                } else {
                    if($lock2 = ip_deprecated_mysql_fetch_assoc($rs)){
                        if ($lock2['value']) {
                            $answer = '+';
                        } else {
                            $answer = '-';
                        }
                    } else {
                        trigger_error("Can not get bool field data. ".$sql);
                    }
                }
            }

            if($lock['type'] == 'textarea'){
                $sql = "select value from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                $rs = ip_deprecated_mysql_query($sql);
                if (!$rs) {
                    trigger_error("Can not get textarea field data. ".$sql." ".ip_deprecated_mysql_error());
                } else {
                    if($lock2 = ip_deprecated_mysql_fetch_assoc($rs)) {
                        $answer = $lock2['value'];
                    } else {
                        trigger_error("Can not get textarea field data. ".$sql);
                    }
                }
            }

            if($lock['type'] == 'lang'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = ip_deprecated_mysql_query($sql2);
                if (!$rs2) {
                    trigger_error("Can not get language field data. ".$sql2." ".ip_deprecated_mysql_error());
                } else {
                    $values = array();
                    while($lock2 = ip_deprecated_mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = \Ip\Lib\StdMod\StdModDb::languages();

                    foreach($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = ip_deprecated_mysql_query($sql3);
                        if($rs3){
                            if ($lock3 = ip_deprecated_mysql_fetch_assoc($rs3)) {
                                $answer .= '/'.$lock3['translation'];
                            }
                        } else {
                            trigger_error("Can't get all languages ".$sql3." ".ip_deprecated_mysql_error());
                        }
                    }


                }
            }

            if($lock['type'] == 'lang_textarea'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = ip_deprecated_mysql_query($sql2);
                if (!$rs2) {
                    trigger_error("Can not get language field data. ".$sql2." ".ip_deprecated_mysql_error());
                } else {
                    $values = array();
                    while($lock2 = ip_deprecated_mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = \Ip\Lib\StdMod\StdModDb::languages();

                    foreach ($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = ip_deprecated_mysql_query($sql3);
                        if($rs3){
                            if($lock3 = ip_deprecated_mysql_fetch_assoc($rs3)) {
                                $answer .= '/'.substr($lock3['translation'], 0, 20);
                            }
                        } else {
                            trigger_error("Can't get all languages ".$sql3." ".ip_deprecated_mysql_error());
                        }

                    }


                }
            }

            if($lock['type'] == 'lang_wysiwyg'){
                $answer = '';
                $sql2 = "select t.translation, l.d_long, t.id as t_id, l.id as l_id from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                $rs2 = ip_deprecated_mysql_query($sql2);
                if (!$rs2) {
                    trigger_error("Can not get language field data. ".$sql2." ".ip_deprecated_mysql_error());
                } else {
                    $values = array();
                    while($lock2 = ip_deprecated_mysql_fetch_assoc($rs2)){
                        $values[$lock2['l_id']] = $lock2['translation'];
                    }

                    $languages = \Ip\Lib\StdMod\StdModDb::languages();

                    foreach ($languages as $key => $language){
                        $sql3 = "select t.translation from `".DB_PREF."par_lang` t, `".DB_PREF."language` l where l.id = '".$language['id']."' and t.language_id = l.id and t.parameter_id = '".$lock['id']."' ";
                        $rs3 = ip_deprecated_mysql_query($sql3);
                        if($rs3){
                            if ($lock3 = ip_deprecated_mysql_fetch_assoc($rs3)) {
                                $answer .= '/'.substr($lock3['translation'], 0, 20);
                            }
                        } else {
                            trigger_error("Can't get all languages ".$sql3." ".ip_deprecated_mysql_error());
                        }
                    }


                }
            }

        }


        return htmlspecialchars($answer);
    }

    function checkField($prefix, $action, $area){
        return null;
    }

    function getParameters($action, $prefix, $area){
        return array("name"=>"type", "value"=>$_REQUEST[''.$prefix]);
    }

    public function processInsert($prefix, $lastInsertId, $area) {
        $last_insert_id = $lastInsertId;

        global $std_mod_db;
        switch($_REQUEST[''.$prefix]){
            case "string_wysiwyg":
                $sql = "insert into `".DB_PREF."par_string` set value = '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_wysiwyg'])."', parameter_id = ".$last_insert_id."";
                $rs = ip_deprecated_mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                break;
            case "string":
                $sql = "insert into `".DB_PREF."par_string` set value = '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_string'])."', parameter_id = ".$last_insert_id."";
                $rs = ip_deprecated_mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                break;
            case "integer":
                if($_REQUEST[$prefix.'_integer'] == '')
                $value = ' NULL ';
                else
                $value = " '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_integer'])."' ";

                $sql = "insert into `".DB_PREF."par_integer` set value = ".$value.", parameter_id = ".$last_insert_id."";
                $rs = ip_deprecated_mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                break;
            case "bool":
                if(isset($_REQUEST[$prefix.'_bool']))
                $value = ' 1 ';
                else
                $value = " 0 ";

                $sql = "insert into `".DB_PREF."par_bool` set value = ".$value.", parameter_id = ".$last_insert_id."";
                $rs = ip_deprecated_mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                break;
            case "textarea":
                $sql = "insert into `".DB_PREF."par_string` set value = '".ip_deprecated_mysql_real_escape_string(str_replace("\r", '', $_REQUEST[$prefix.'_textarea']))."', parameter_id = ".$last_insert_id."";
                $rs = ip_deprecated_mysql_query($sql);
                if(!$rs)
                trigger_error("Can't insert parameter ".$sql." ".ip_deprecated_mysql_error());
                break;

            case "lang":
                $languages = \Ip\Lib\StdMod\StdModDb::languages();
                foreach($languages as $key => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_lang_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = ".$last_insert_id." ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                }
                break;
            case "lang_textarea":
                $languages = \Ip\Lib\StdMod\StdModDb::languages();
                foreach($languages as $key => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_lang_textarea_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = ".$last_insert_id." ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                }
                break;
            case "lang_wysiwyg":
                $languages = \Ip\Lib\StdMod\StdModDb::languages();
                foreach($languages as $key => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_lang_wysiwyg_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = ".$last_insert_id." ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                }
                break;
        }
    }

    public function processUpdate($prefix, $rowId, $area) {
        $key = $rowId;
        global $std_mod_db;


        if($_REQUEST[''.$prefix] == 'string_wysiwyg'){
            $sql = "update `".DB_PREF."par_string` set value='".ip_deprecated_mysql_real_escape_string($_REQUEST[''.$prefix.'_string'])."' where parameter_id = '".$key."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".ip_deprecated_mysql_error());
        }
        if($_REQUEST[''.$prefix] == 'string'){
            $sql = "update `".DB_PREF."par_string` set value='".ip_deprecated_mysql_real_escape_string($_REQUEST[''.$prefix.'_string'])."' where parameter_id = '".$key."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".ip_deprecated_mysql_error());
        }

        if($_REQUEST[''.$prefix] == 'integer'){
            if($_REQUEST[''.$prefix.'_integer'] != '')
            $sql = "update `".DB_PREF."par_integer` set value='".ip_deprecated_mysql_real_escape_string($_REQUEST[''.$prefix.'_integer'])."' where parameter_id = '".$key."' ";
            else
            $sql = "update `".DB_PREF."par_integer` set value=NULL where parameter_id = '".$key."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".ip_deprecated_mysql_error());
        }

        if($_REQUEST[''.$prefix] == 'bool'){
            if(isset($_REQUEST[$prefix.'_bool']))
            $value = ' 1 ';
            else
            $value = " 0 ";

            $sql = "update `".DB_PREF."par_bool` set value=".$value." where parameter_id = '".$key."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".ip_deprecated_mysql_error());
        }

        if($_REQUEST[''.$prefix] == 'textarea'){
            $sql = "update `".DB_PREF."par_string` set value='".ip_deprecated_mysql_real_escape_string(str_replace("\r", "", $_REQUEST[''.$prefix.'_string']))."' where parameter_id = '".$key."' ";
            $rs = ip_deprecated_mysql_query($sql);
            if(!$rs)
            trigger_error("Can't update parameter ".$sql." ".ip_deprecated_mysql_error());
        }


        if($_REQUEST[''.$prefix] == 'lang'){
            $languages = \Ip\Lib\StdMod\StdModDb::languages();
             
            $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$key."' ";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if($rs2){
                foreach($languages as $key2 => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', language_id = '".$language['id']."',  parameter_id = ".$key." ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                }
            }else
            trigger_error("Can't update parameter ".$sql2." ".ip_deprecated_mysql_error());
             
             
             
        }

        if($_REQUEST[''.$prefix] == 'lang_textarea'){
            $languages = \Ip\Lib\StdMod\StdModDb::languages();
             
            $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$key."' ";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if($rs2){
                foreach($languages as $key2 => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = '".$key."' ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                }
            }else
            trigger_error("Can't update parameter ".$sql2." ".ip_deprecated_mysql_error());
        }

        if($_REQUEST[''.$prefix] == 'lang_wysiwyg'){
            $languages = \Ip\Lib\StdMod\StdModDb::languages();
             
            $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$key."' ";
            $rs2 = ip_deprecated_mysql_query($sql2);
            if($rs2){
                foreach($languages as $key2 => $language){
                    $sql3 = "insert into `".DB_PREF."par_lang` set translation = '".ip_deprecated_mysql_real_escape_string($_REQUEST[$prefix.'_'.$language['id']])."', language_id = '".$language['id']."', parameter_id = '".$key."' ";
                    $rs3 = ip_deprecated_mysql_query($sql3);
                    if(!$rs3)
                    trigger_error("Can't update parameter ".$sql3." ".ip_deprecated_mysql_error());
                }
            }else
            trigger_error("Can't update parameter ".$sql2." ".ip_deprecated_mysql_error());
        }


    }
    public function processDelete($area, $id) {
        $key = $id;
        global $std_mod_db;
        $sql = "select * from `".DB_PREF."".$area->dbTable."` where ".$area->dbPrimaryKey." = '".$key."' ";
        $rs = ip_deprecated_mysql_query($sql);
        if($rs){
            if($lock = ip_deprecated_mysql_fetch_assoc($rs)){

                if($lock['type'] == 'string_wysiwyg'){
                    $sql = "delete from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".ip_deprecated_mysql_error());
                }
                if($lock['type'] == 'string'){
                    $sql = "delete from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".ip_deprecated_mysql_error());
                }


                if($lock['type'] == 'textarea'){
                    $sql = "delete from `".DB_PREF."par_string` where parameter_id = '".$lock['id']."' ";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".ip_deprecated_mysql_error());
                }
                if($lock['type'] == 'bool'){
                    $sql = "delete from `".DB_PREF."par_bool` where parameter_id = '".$lock['id']."' ";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".ip_deprecated_mysql_error());
                }

                if($lock['type'] == 'integer'){
                    $sql = "delete from `".DB_PREF."par_integer` where parameter_id = '".$lock['id']."' ";
                    $rs = ip_deprecated_mysql_query($sql);
                    if(!$rs)
                    trigger_error("Can't delete parameter ".$sql." ".ip_deprecated_mysql_error());
                }
                if($lock['type'] == 'lang'){
                    $languages = \Ip\Lib\StdMod\StdModDb::languages();

                    $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$lock['id']."' ";
                    $rs2 = ip_deprecated_mysql_query($sql2);
                    if(!$rs2)
                    trigger_error("Can't delete parameter ".$sql2." ".ip_deprecated_mysql_error());

                }

                if($lock['type'] == 'lang_textarea'){
                    $languages = \Ip\Lib\StdMod\StdModDb::languages();

                    $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$lock['id']."' ";
                    $rs2 = ip_deprecated_mysql_query($sql2);
                    if(!$rs2)
                    trigger_error("Can't delete parameter ".$sql2." ".ip_deprecated_mysql_error());

                }

                if($lock['type'] == 'lang_wysiwyg'){
                    $languages = \Ip\Lib\StdMod\StdModDb::languages();

                    $sql2 = "delete from `".DB_PREF."par_lang` where parameter_id = '".$lock['id']."' ";
                    $rs2 = ip_deprecated_mysql_query($sql2);
                    if(!$rs2)
                    trigger_error("Can't delete parameter ".$sql2." ".ip_deprecated_mysql_error());

                }

            } else trigger_error("Can't delete parameter ".$sql);
        }else trigger_error("Can't delete parameter ".$sql." ".ip_deprecated_mysql_error());

    }






}

