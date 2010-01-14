<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management;   
 

if (!defined('CMS')) exit;

class Config // extends MimeType
{
  static function getMceStyles()
  {
    $tinyMceStyles = array();
    $tinyMceStyles[] = array('translation'=>'Text', 'css_style'=>'');
    $tinyMceStyles[] = array('translation'=>'Caption', 'css_style'=>'caption');
    $tinyMceStyles[] = array('translation'=>'Signature', 'css_style'=>'signature');
    $tinyMceStyles[] = array('translation'=>'Note', 'css_style'=>'note');
    return $tinyMceStyles;
  }
}



