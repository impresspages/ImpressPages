<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\separator;   
 
if (!defined('CMS')) exit;

class Config // extends MimeType
{
  static function getLayouts()
  {
    global $parametersMod;
    $layouts = array();
    $layouts[] = array('translation'=>$parametersMod->getValue('standard', 'content_management', 'widget_separator', 'layout_line'), 'name'=>'line');
    $layouts[] = array('translation'=>$parametersMod->getValue('standard', 'content_management', 'widget_separator', 'layout_space'), 'name'=>'space');
    return $layouts;
  }
}