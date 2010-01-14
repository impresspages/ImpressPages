<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\standard\content_management\Widgets\misc\html_code;   
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;
class Template {

  public static function generateHtml($text, $manager = false, $layout=null){
		$answer = '';
		$parts = array();
		if($manager){
			$parts = preg_split('/<object/i', $text);
			$i = 0;
			while($i < sizeof($parts)){
				$parts[$i] = Template::strChangeWmode($parts[$i]);
				$i++;
			}
			$i = 0;
			while($i < sizeof($parts)){
				if($i > 0)
					$answer .= "<object ";
				$answer .= $parts[$i];
				$i++;
			}
		}else
			$answer = $text;
			
    switch($layout){
      default:
      case "default":  			
        return '<div class="ipWidget ipWidgetHtmlCode">'.$answer.'</div>';
      break;
    }
  }
	 
	 
	public static function strChangeWmode($embed_html){
			$embed_html = str_ireplace("\"transparent\"", "opaque",$embed_html);
			$embed_html = str_ireplace("\"window\"", "opaque",$embed_html);
			$embed_html = str_ireplace("</object>", '<param name="wmode" value="opaque"></object>',$embed_html);
			$embed_pos = stripos($embed_html, '<embed ');
			if($embed_pos)
				$embed_end_pos = stripos($embed_html,'>', $embed_pos + 1);
			if(isset($embed_end_pos) && $embed_end_pos)
				$embed_html = substr($embed_html, 0, $embed_end_pos).' wmode="opaque" '.substr($embed_html, $embed_end_pos);
			return $embed_html;
	 }
	 
}

