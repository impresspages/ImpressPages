<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\separator;   
 
if (!defined('CMS')) exit;
class Template {

  public static function generateHtml($layout=null){
    switch($layout){
      default:
      case "line":     
        return "\n".'<div class="ipWidget ipWidgetSeparator ipWidgetSeparatorLine"></div>'."\n";
      break;
      case "space":     
        return "\n".'<div class="ipWidget ipWidgetSeparator ipWidgetSeparatorSpace"></div>'."\n";
      break;
    }    
  }
	 
}

