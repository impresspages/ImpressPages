<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\developer\config_exp_imp;


class FieldLanguages extends \Library\Php\Form\Field{
    function genHtml($class){

        $answer = '';

        $rs = mysql_query("select * from `".DB_PREF."language` where 1 order by row_number");
        if($rs){
            $first = true;
            while($lock = mysql_fetch_assoc($rs)){
                if($first)
                $checked = ' checked ';
                else
                $checked = '';
                $answer .= '<div><input '.$checked.' type="radio" class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'" value="'.htmlspecialchars($lock['id']).'"/> '.htmlspecialchars($lock['d_short']).'</div>';
                $first = false;
            }
        }else
        trigger_error($sql." ".mysql_error());
        return $answer;
    }
}


class FieldModules extends \Library\Php\Form\Field{
    function genHtml($class){
        global $cms;

        $answer = '';
        $moduleGroups = \Ip\Backend\Db::modules();
        foreach($moduleGroups as $key => $group){
            $answer .= '<div class="moduleGroup">'.htmlspecialchars($key).'</div>';
            foreach($group as $key2 => $module){
                if(isset($_POST[$this->name.'['.$module['id'].']']) || $this->value)
                $answer .= '<div class="module"><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'['.$module['m_name'].']['.$module['g_name'].']" value="'.$module['id'].'"/> '.htmlspecialchars($module['translation']).'</div>';
                else
                $answer .= '<div class="module"><input class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'['.$module['g_name'].']['.$module['m_name'].']"  value="'.$module['id'].'"/>'.htmlspecialchars($module['translation'])."</div>";
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
}


class FieldTypes extends \Library\Php\Form\Field{
    function genHtml($class){
        global $parametersMod;
        $answer = '';

        $answer .= '

     
      <div><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'[string]" value="1"/> '.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_string')).'</div>
      <div><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'[integer]" value="1"/> '.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_integer')).'</div>
      <div><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'[bool]" value="1"/> '.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_bool')).'</div>
      <div><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'[textarea]" value="1"/> '.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_textarea')).'</div>
      <div><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'[string_wysiwyg]" value="1"/> '.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_wysiwyg')).'</div>
      <div><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'[lang]" value="1"/> '.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_string_lang')).'</div>
      <div><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'[lang_textarea]" value="1"/> '.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_textarea_lang')).'</div>
      <div><input checked class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'[lang_wysiwyg]" value="1"/> '.htmlspecialchars($parametersMod->getValue('developer', 'std_mod', 'admin_translations', 'parameter_wysiwyg_lang')).'</div>
      
      
      
      
      '
      
      
      ;
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


