<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\photo;   
 
if (!defined('CMS')) exit;
class Template {

  public static function generateHtml($title, $photo, $layout = null){
    $localPhoto = str_replace(BASE_URL, BASE_DIR, $photo);   //getImageSize may not work with URL in some configurations
    switch($layout){
      default:
      case "default": 
        if($photo){
          $info = getimagesize($localPhoto);
          return '
<div class="ipWidget ipWidgetPhoto">
  <img width="'.$info[0].'" height="'.$info[1].'" class="ipWidgetPhotoImage" alt="'.htmlspecialchars($title).'" src="'.$photo.'" />
</div>
';
        }    
        break;
    }
  }
	 
}

