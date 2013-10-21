<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Library\Php\Text;

/**
 * replaces special characters in a string
 * @package Library
 */
class SystemVariables
{
    public static function insert($text, $languageId = null){
        global $parametersMod;
        $answer = $text;

        $answer = str_replace('[[site_name]]', $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name', $languageId), $answer);
        $answer = str_replace('[[site_email]]', '<a href="mailto:'.$parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email', $languageId).'">'.$parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email', $languageId).'</a>', $answer);

        return $answer;
    }

    //clear unknown tags
    public static function clear($text){
        return preg_replace('/\[\[[^\[\]]*\]\]/', '', $text);
    }

}