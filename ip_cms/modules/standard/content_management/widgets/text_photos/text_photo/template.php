<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\text_photo;   
 
if (!defined('CMS')) exit;
class Template {

  public static function generateHtml($title, $photo, $photo_big, $text, $layout=null){
		$localPhoto = str_replace(BASE_URL, BASE_DIR, $photo);   //getImageSize may not work with URL in some configurations
    switch($layout){
      default:
      case "default":
    
        $text = str_replace('<br>', '<br />', $text);
        
        
        if ($photo){
          $info = getimagesize($localPhoto);
          $image = '<a class="ipWidgetTextPhotoImageLeft" href="'.$photo_big.'" rel="lightbox[ipWidget]" title="'.htmlspecialchars($title).'"><img width="'.$info[0].'" height="'.$info[1].'" src="'.$photo.'" alt="'.htmlspecialchars($title).'" /></a>';
        }else{
          $image = '';
        }
        return ' 
<div class="ipWidget ipWidgetTextPhoto ipWidgetTextPhotoLeft">
  '.$image.'
  <div class="ipWidgetTextPhotoText">
    '.$text.'
  </div> 
  <div class="clear"><!-- --></div>
</div>
';
    
      break;
      case "right":
    
        $text = str_replace('<br>', '<br />', $text);
        
        
        if ($photo){
          $info = getimagesize($localPhoto);
          $image = '<a class="ipWidgetTextPhotoImageRight" href="'.$photo_big.'" rel="lightbox[ipWidget]" title="'.htmlspecialchars($title).'"><img width="'.$info[0].'" height="'.$info[1].'" src="'.$photo.'" alt="'.htmlspecialchars($title).'" /></a>';
        }else{
          $image = '';
        }
        return ' 
<div class="ipWidget ipWidgetTextPhoto ipWidgetTextPhotoRight">
  '.$image.'
  <div class="ipWidgetTextPhotoText">
    '.$text.'
  </div> 
  <div class="clear"><!-- --></div>
</div>
';
    
      break;
      case "left_small":
    
        $text = str_replace('<br>', '<br />', $text);
        
        
        if ($photo)
          $image = '<img class="ipWidgetTextPhotoImageSmallLeft" src="'.$photo.'" alt="'.htmlspecialchars($title).'" />';
        else
          $image = '';
        return '
<div class="ipWidget ipWidgetTextPhoto ipWidgetTextPhotoLeftSmall">
  '.$image.'
  <div class="ipWidgetTextPhotoText">
    '.$text.'
  </div> 
  <div class="clear"><!-- --></div>
</div>
';
    
      break;      
      case "right_small":
        $text = str_replace('<br>', '<br />', $text);
        
        
        if ($photo)
          $image = '<img class="ipWidgetTextPhotoImageSmallRight" src="'.$photo.'" alt="'.htmlspecialchars($title).'" />';
        else
          $image = '';
        return ' 
<div class="ipWidget ipWidgetTextPhoto ipWidgetTextPhotoRightSmall">
  '.$image.'
  <div class="ipWidgetTextPhotoText">
    '.$text.'
  </div> 
  <div class="clear"><!-- --></div>
</div>
';
    
      break;
    }
    return $answer;
    
  }
	 
}

