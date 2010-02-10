<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\search;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


class Template{


  public static function searchForm($caption, $value, $button, $url){
    global $parametersMod;
    return '

			<form id="modAdministratorSearchForm" method="post" action="'.$url.'"> 
				<div>
  				<input type="hidden" name="action" value="search" />
  				<input type="text" name="q" value="'.htmlspecialchars($value).'" class="modAdministratorSearchInput" /> 
					<a title="'.htmlspecialchars($parametersMod->getValue('administrator', 'search', 'translations', 'search')).'" href="#" class="modAdministratorSearchButton" onclick="document.getElementById(\'modAdministratorSearchForm\').submit(); return false;"></a>
				</div>
			</form>    
    ';    
  }
  
  public static function noSearchString($title, $text){
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    
    $answer = '';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title, 1);
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text);
    return $answer;
  }
  
  
  public static function noResults($title, $text){
    global $site;
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');

    $answer = '';
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title, 1);
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml($text);
    return $answer;
  }
  
  public static function searchResult($title, $foundElementsCombined, $foundElements){
    global $site;
    global $parametersMod;
    
    $site->requireTemplate('standard/content_management/widgets/text_photos/title/template.php');
    $site->requireTemplate('standard/content_management/widgets/text_photos/text/template.php');
    
    $answer ='';
    
    $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($title, 1);
    $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml(Template::elementsList($foundElementsCombined));
    
    foreach ($foundElements as $zoneKey => $zoneBunch) {
      $answer .= \Modules\standard\content_management\Widgets\text_photos\title\Template::generateHtml($site->getZone($zoneKey)->getTitle(), 2);
      $answer .= \Modules\standard\content_management\Widgets\text_photos\text\Template::generateHtml(Template::elementsList($zoneBunch));
    }

    return $answer;  
  }
  
  public static function elementsList($elements){
    global $parametersMod;
    $answer = '';
    $answer .= '<ul class="modAdministratorSearchList">';
    foreach ($elements as $key => $element) {
		  $answer .= '<li>';
      $tmpTitle = $element->getPageTitle();
      if($tmpTitle == '')
        $tmpTitle = $element->getButtonTitle(); 
			$answer .= '<a class="modAdministratorSearchLink" href="'.$element->getLink().'">'.htmlspecialchars($tmpTitle).'</a>';
			if($parametersMod->getValue('administrator', 'search', 'options', 'show_description'))
				$answer .= '<p class="modAdministratorSearchDescription">'.htmlspecialchars($element->getDescription()).'</p>';
      $answer .= '</li>'."\n";
    }
    $answer .= '</ul>';
    return $answer;
  }

}

