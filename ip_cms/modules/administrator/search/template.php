<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\search;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/model.php');

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
        $answer = '';
        $answer .= \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData('IpTitle', array("title" => $title));
        $answer .= \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData('IpText', array("text" => $text));
        return $answer;
    }


    public static function noResults($title, $text){

        $answer = '';
        $answer .= \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData('IpTitle', array("title" => $title));
        $answer .= \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData('IpText', array("text" => $text));
        return $answer;
    }

    public static function searchResult($title, $foundElementsCombined, $foundElements){        

        $answer = '';
        $answer .= \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData('IpTitle', array("title" => $title));
        $answer .= \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData('IpText', array("text" => Template::elementsList($foundElementsCombined)));

        foreach ($foundElements as $zoneKey => $zoneBunch) {
            $answer .= \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData('IpTitle', array("title" => $site->getZone($zoneKey)->getTitle()), 'level2');
            $answer .= \Modules\standard\content_management\Model::generateWidgetPreviewFromStaticData('IpText', array("text" => Template::elementsList($zoneBunch)));
        }

        return $answer;
    }

    public static function elementsList($elements){
        global $parametersMod;
        $answer = "\n";
        $answer .= '<ul class="modAdministratorSearchList">'."\n";
        foreach ($elements as $key => $element) {
            $answer .= '  <li>'."\n";
            $tmpTitle = $element->getPageTitle();
            if($tmpTitle == ''){
                $tmpTitle = $element->getButtonTitle();
            }
            $answer .= '    <a class="modAdministratorSearchLink" href="'.$element->getLink().'">'.htmlspecialchars($tmpTitle).'</a>'."\n";
            if($parametersMod->getValue('administrator', 'search', 'options', 'show_description')){
                $answer .= '    <p class="modAdministratorSearchDescription">'.htmlspecialchars($element->getDescription()).'</p>'."\n";
            }
            $answer .= '  </li>'."\n";
        }
        $answer .= '</ul>'."\n";
        return $answer;
    }

}

