<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\developer\localization;


class FieldLanguages extends \Library\Php\Form\Field{
    function genHtml($class){
        global $parametersMod;

        $answer = '';

        $answer .= '<div><input checked="on" type="radio" class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'" value="backend"/> '.htmlspecialchars($parametersMod->getValue('Config.administrator_interface')).'</div>';

        $rs = mysql_query("select * from `".DB_PREF."language` where 1 order by row_number");
        if($rs){
            while($lock = ip_deprecated_mysql_fetch_assoc($rs)){
                $answer .= '<div><input  type="radio" class="'.$class.' checkbox" type="checkbox" name="'.$this->name.'" value="'.htmlspecialchars($lock['id']).'"/> '.htmlspecialchars($parametersMod->getValue('developer', 'localization', 'admin_translations', 'public_interface').' ('.$lock['d_long']).')</div>';
                $first = false;
            }
        }else
        trigger_error($sql." ".ip_deprecated_mysql_error());
        return $answer;
    }
}


