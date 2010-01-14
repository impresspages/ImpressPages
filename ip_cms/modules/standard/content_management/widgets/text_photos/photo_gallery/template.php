<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\photo_gallery;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {

  public static function generateHtml($photos, $layout = null){
    switch($layout){
      default:
      case "default":
        $answer = '
        <div class="ipWidget ipWidgetPhotoGallery"><div class="ipWidgetPhotoGalleryWrapper">'."\n";
        foreach($photos as $key => $lock){
          $answer .= '<a class="ipWidgetPhotoGalleryImage" href="'.$lock['photo_big'].'" rel="lightbox[ipWidget]" title="'.htmlspecialchars($lock['title']).'"><img alt="'.htmlspecialchars($lock['title']).'" src="'.$lock['photo'].'"/></a>'."\n";
        }
        $answer .= '</div><div class="clear"><!-- --></div></div>'."\n";
      break;
    }
    return $answer;
  }
	 
}

