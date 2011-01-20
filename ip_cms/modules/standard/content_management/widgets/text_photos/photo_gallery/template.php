<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\photo_gallery;   
 
if (!defined('CMS')) exit;
class Template {

  public static function generateHtml($photos, $layout = null){
    switch($layout){
      default:
      case "default":
        $galleryHtml = '';
        foreach($photos as $key => $lock){
          $galleryHtml .= '    <a class="ipWidgetPhotoGalleryImage" href="'.$lock['photo_big'].'" rel="lightbox[ipWidget]" title="'.htmlspecialchars($lock['title']).'"><img alt="'.htmlspecialchars($lock['title']).'" src="'.$lock['photo'].'"/></a>'."\n";
        }
        $answer = '
<div class="ipWidget ipWidgetPhotoGallery">
  <div class="ipWidgetPhotoGalleryWrapper">
    '.$galleryHtml.'       
  </div>
  <div class="clear"><!-- --></div>
</div>
';
      break;
    }
    return $answer;
  }
	 
}

