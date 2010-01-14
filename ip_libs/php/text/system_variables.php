<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Library\Php\Text;

/**
 * replaces special characters in a string
 * @package Library
 */
class SystemVariables
{
  public static function insert($text){
    global $parametersMod;
    $answer = $text;
    
    $answer = str_replace('[[site_name]]', $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'), $answer);
    $answer = str_replace('[[site_email]]', '<a href="mailto:'.$parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email').'">'.$parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email').'</a>', $answer);
    
    return $answer;    
  }
  
  //clear unknown tags
  public static function clear($text){
    return preg_replace('/\[\[[^\[\]]*\]\]/', '', $text);  
  }
  
}