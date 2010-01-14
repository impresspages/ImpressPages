<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\text_photos\faq;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {

  public static function generateHtml($id, $title, $text, $layout = null){
    $text = $text;
    if (strpos($text, '<p') > 10 || strpos($text, '<p') === false)
      $text = "<p>".$text."</p>";
    $text = str_replace('<br>', '<br />', $text);
    
    switch($layout){
      default:
      case "default":  
        return '
        <div class="ipWidget ipWidgetFaq">
          <a href="#" onclick="ipWidgetFaqShow('.$id.'); return false;" class="ipWidgetFaqQuestion">'.htmlspecialchars($title).'</a>
          <div id="ipWidgetFaqAnswer-'.$id.'" class="ipWidgetFaqAnswer">'.$text.'</div>            
        </div>';
      break;
    }
  }
	 
  public static function initHtml(){
    return '
      <script type="text/javascript">
         function ipWidgetFaqShow(id){
           element = document.getElementById("ipWidgetFaqAnswer-" + id);
           if (element.style.display != "block")
              element.style.display = "block";
           else
              element.style.display = "none";
         }
         //]]>
      </script>
    ';
  }	 
}

