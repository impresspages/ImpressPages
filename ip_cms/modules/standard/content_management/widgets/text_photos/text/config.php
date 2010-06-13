<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\text;   
 
if (!defined('CMS')) exit;

class Config // extends MimeType
{
  static function getLayouts()
  {
    global $parametersMod;
    $layouts = array();
    $layouts[] = array('translation'=>$parametersMod->getValue('standard', 'content_management', 'widget_text', 'layout_default'), 'name'=>'default');
    return $layouts;
  }


  static function getMceInit(){
    global $site;
    $site->requireConfig('standard/content_management/config.php');
    return \Modules\standard\content_management\Config::getMceInit();
  }

}