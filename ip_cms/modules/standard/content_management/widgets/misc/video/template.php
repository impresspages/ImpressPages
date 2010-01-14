<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\video;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {

  public static function generateHtml($title, $file, $manager, $layout=null){
    require_once(BASE_DIR.LIBRARY_DIR.'php/php-flvinfo/flvinfo.php');
    if($file == '' || $file == VIDEO_DIR)
      return '';    
    $flvinfo = new \Flvinfo();
    if(!$flvinfo)
      return '';
	  $info = $flvinfo->getInfo($file, true);
    
    switch($layout){
      default:
      case "default":  
	  
        if(isset($info->video)){
          $width = $info->video->width;
          $height = $info->video->height;
      
      
        //$height = $info->video->height + 37;  //if controls are placed under video.
          if($manager){
            return '
           <div class="ipWidget ipWidgetVideo">
             <table align="left" cellspacing="0" cellpaddding="0"><tr><td>
              <object align="left" class="block" scale="noscale" salign="tl" width="'.$width.'" height="'.$height.'">
              <param name="movie" value="'.BASE_URL.$file.'">
              <param name="scale" value="noscale">
              <param name="salign" value="tl">
              <param name="FlashVars" value="video_width_emb='.$width.'&video_height_emb='.$height.'&bg=0xD4D6D7&file='.BASE_URL.$file.'&player_skin=".BASE_URL.LIBRARY_DIR."flash/SkinOverPlayStopSeekFullVol.swf">
              <embed src="'.BASE_URL.LIBRARY_DIR.'flash/video_player/player.swf" scale="noscale" salign="tl" width="'.$width.'" bg="0xD4D6D7" height="'.$height.'"
                 FlashVars="video_width_emb='.$width.'&video_height_emb='.$height.'&file='.BASE_URL.$file.'&bg=0xD4D6D7&player_skin='.BASE_URL.LIBRARY_DIR.'flash/video_player/SkinOverPlayStopSeekFullVol.swf"
                  wmode="transparent">
              </embed>
              </object>
            </td></tr></table>
            <div class="clear"><!-- --></div>
         </div>
              
              ';    
          
          }else{
            return "
          <div class=\"ipWidget ipWidgetVideo\">
           <script type=\"text/javascript\">
            if (AC_FL_RunContent == 0) {
              alert(\"This page requires AC_RunActiveContent.js.\");
            } else {
              AC_FL_RunContent(
                'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0',
                'width', '".$width."',
                'height', '".$height."',
                'src', '".BASE_URL.LIBRARY_DIR."flash/video_player/player',
                'quality', 'high',
                'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
                'play', 'true',
                'loop', 'true',
                'scale', 'noscale',
                'wmode', 'window',
                'devicefont', 'false',
                'id', 'player',
                'bgcolor', '#ffffff',
                'name', '".BASE_URL.LIBRARY_DIR."flash/video_player/player',
                'content', 'true',
                'allowFullScreen', 'true',
                'allowScriptAccess','sameDomain',
                'movie', '".BASE_URL.LIBRARY_DIR."flash/video_player/player',
                'salign', 'tl',
                'flashvars', 'video_width_emb=".$width."&video_height_emb=".$height."&file=".BASE_URL.$file."&player_skin=".BASE_URL.LIBRARY_DIR."flash/video_player/SkinOverPlayStopSeekFullVol.swf&bg=0xD4D6D7'
                );
             }
             //]]>
            </script>
           <div class=\"clear\"></div>
          </div>      
            ";
          }
        } else {
          return '<div class=\"ipWidget ipWidgetVideo\">'.htmlspecialchars($title).'&nbsp;</div>';
          
        }
      
       break;
    }       
    

	}
	
	public static function initHtml(){
	 return '<script src="'.BASE_URL.LIBRARY_DIR.'js/AC_RunActiveContent.js" type="text/javascript"></script>';
	}
}

