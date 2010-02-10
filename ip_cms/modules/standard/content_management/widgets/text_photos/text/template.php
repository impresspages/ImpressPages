<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\text;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {

  public static function generateHtml($text, $layout = null){
    $text = str_replace('<br>', '<br />', $text);

    switch($layout){
      default:
      case "default":     
        return '<div class="ipWidget ipWidgetText">'.$text.'</div>';
      break;
    }
  }
	 
}

