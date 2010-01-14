<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\text_photo;   
 
if (!defined('CMS')) exit;

class Config // extends MimeType
{
  static function getLayouts()
  {
    global $parametersMod;
    $layouts = array();
    $layouts[] = array('translation'=>$parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'layout_left'), 'name'=>'left');
    $layouts[] = array('translation'=>$parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'layout_right'), 'name'=>'right');
    $layouts[] = array('translation'=>$parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'layout_small_left'), 'name'=>'left_small');
    $layouts[] = array('translation'=>$parametersMod->getValue('standard', 'content_management', 'widget_text_photo', 'layout_small_right'), 'name'=>'right_small');
    return $layouts;
  }
}