<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\logo_gallery;   
 
if (!defined('CMS')) exit;

class Template {

  public static function generateHtml($photos, $layout){

    switch($layout){
      default:
      case "default":  
        $galleryHtml = '';
        foreach($photos as $key => $lock){
          if($lock['link'] != '' && strpos($lock['link'], "http://") !== 0)
            $lock['link'] = 'http://'.$lock['link'];
            
          $lock['link'] = str_replace('<', '', $lock['link']);  
          $lock['link'] = str_replace('>', '', $lock['link']);  
          $lock['link'] = str_replace('"', '\"', $lock['link']);  
          
          if($lock['link'])
            $galleryHtml .= '    <a rel="nofollow" href="'.$lock['link'].'"><span class="ipWidgetLogoGalleryLogo" style="background: url('.$lock['logo'].') no-repeat scroll center center;"></span></a>'."\n";
          else
            $galleryHtml .= '    <span class="ipWidgetLogoGalleryLogo" style="background: url('.$lock['logo'].') no-repeat scroll center center;"></span>'."\n";
        }
        
        $answer = '
<div class="ipWidget ipWidgetLogoGallery">
  <div class="ipWidgetLogoGalleryWrapper">
    '.$galleryHtml.'
  </div>  
  <div class="clear"><!-- --></div>
</div>
';
        return $answer;
      break;
    }
        
  }
	 
}

