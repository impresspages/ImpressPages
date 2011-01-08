<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

/**
 * Common class to generate breadcrumb trail.
 * @package Library
 */ 

namespace Library\Php\Breadcrumb;  
 
if (!defined('CMS')) exit;
 
class Common{
  public static function generate($separator = null){
    global $site;
    
    $answer = '';
    $elements = $site->getBreadcrumb();
    foreach($elements as $key => $element){
      if($answer .= '' && $separator != null)
        $answer .= $separator;
      $answer .= '<a href="'.$element['link'].'">'.$element['page_title'].'</a>';
    }
    return $answer;
  }  
}

