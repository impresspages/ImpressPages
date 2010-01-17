<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\photo;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {

  public static function generateHtml($title, $photo, $layout = null){
    switch($layout){
      default:
      case "default": 
        if($photo){
          $info = getimagesize($photo);
          return '<div class="ipWidget ipWidgetPhoto"><img width="'.$info[0].'" height="'.$info[1].'" class="ipWidgetPhotoImage" alt="'.htmlspecialchars($title).'" src="'.$photo.'" /></div>';
        }    
        break;
    }
  }
	 
}

