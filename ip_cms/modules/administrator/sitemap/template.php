<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\sitemap;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/title/template.php');
require_once (BASE_DIR.MODULE_DIR.'standard/content_management/widgets/text_photos/text/template.php');

class Template{


  public static function sitemap($title, $text){
    $answer = '';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title, 1);
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text);
    return $answer;
  }
  
  
  public static function zone($zone, $maxDepth = null){
    global $parametersMod;
    $answer = '';
		if($parametersMod->getValue('administrator', 'sitemap', 'options', 'include_zone_title')){
      $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($zone->getTitle(), 2);
		}
		
		$elements = $zone->getElements();
		if(sizeof($elements) > 0){
		  $curDepth = $elements[0]->getDepth(); 
		  $tmpHtml = Template::tepElements($zone, $elements, $curDepth, $maxDepth);
      $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($tmpHtml);
		}
		
    return $answer;
  }
  
	private static function tepElements($zone, $elements, $curDepth, $maxDepth = null){
	 $answer = '';
		if($maxDepth != null && $curDepth <= $maxDepth){
			if(is_array($elements) && sizeof($elements) > 0){
				foreach($elements as $key => $element){
				  $elementHtml = '';
					$children = $zone->getElements(null, $element->getId());
					$childrenHtml = Template::tepElements($zone, $children, $curDepth+1, $maxDepth); 
					$link = $element->getLink();
					if ($link) {
						$elementHtml .= '<a class="modAdministratorSitemapLink" href="'.$link.'">'.htmlspecialchars($element->getButtonTitle()).'</a>';
					} else {
					  if ($childrenHtml != '') {
  						$elementHtml .= '<a class="modAdministratorSitemapLink">'.htmlspecialchars($element->getButtonTitle()).'</a>';
					  }
					}
					
					$elementHtml .= $childrenHtml;
					
					if ($elementHtml != '') {
	   				$answer .= '<li>'.$elementHtml.'</li>';
  				}
				}
  			if ($answer != '') {
				  $answer = '<ul class="modAdministratorSitemapList">'.$answer.'</ul>';
			  }
			}
		}
		return $answer;
	}	  

}

