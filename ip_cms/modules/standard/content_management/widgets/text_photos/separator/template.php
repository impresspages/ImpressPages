<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\separator;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {

  public static function generateHtml($layout=null){
    switch($layout){
      default:
      case "line":     
        return '<div class="ipWidget ipWidgetSeparatorLine"></div>';
      break;
      case "space":     
        return '<div class="ipWidget ipWidgetSeparatorSpace"></div>';
      break;
    }    
  }
	 
}

