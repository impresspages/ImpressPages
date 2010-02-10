<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\rich_text;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {

  public static function generateHtml($text, $layout = null){
    $text = $text;
    $text = str_replace('<br>', '<br />', $text);
    $text = str_replace('</object>', '<param name="wmode" value="opaque"></object>', $text);

    $text = str_replace('"/'.FILE_DIR.'/repository/', '"'.BASE_URL.FILE_DIR.'repository/', $text);
    $text = str_replace('"/'.IMAGE_DIR.'/repository/', '"'.BASE_URL.IMAGE_DIR.'repository/', $text);
    $text = str_replace('"/'.VIDEO_DIR.'repository/', '"'.BASE_URL.VIDEO_DIR.'repository/', $text);

    
    switch($layout){
      default:
      case "default":
        return '<div class="ipWidget ipWidgetText">'.$text.'<div class="clear"><!-- --></div></div>';
      break;
    }
  }
	 
}

