<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\logo_gallery;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

class Template {

  public static function generateHtml($photos, $layout){

    switch($layout){
      default:
      case "default":  
        $answer = '
        <div class="ipWidget ipWidgetLogoGallery"><div class="ipWidgetLogoGalleryWrapper">
';
        foreach($photos as $key => $lock){
          if($lock['link'] != '' && strpos($lock['link'], "http://") !== 0)
            $lock['link'] = 'http://'.$lock['link'];
            
          $lock['link'] = str_replace('<', '', $lock['link']);  
          $lock['link'] = str_replace('>', '', $lock['link']);  
          $lock['link'] = str_replace('"', '\"', $lock['link']);  
          
          if($lock['link'])
            $answer .= '<a rel="nofollow" href="'.$lock['link'].'"><span class="ipWidgetLogoGalleryLogo" style="background: url('.$lock['logo'].') no-repeat scroll center center;"></span></a>'."\n";
          else
            $answer .= '<span class="ipWidgetLogoGalleryLogo" style="background: url('.$lock['logo'].') no-repeat scroll center center;"></span>'."\n";
        }
        $answer .= '</div><div class="clear"><!-- --></div></div>'."\n";
        return $answer;
      break;
    }
        
  }
	 
}

