<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Modules\administrator\search;

 
if (!defined('CMS')) exit;  


/**
 * 
 *   
 * @package ImpressPages
 */ 

class Element extends \Frontend\Element{

  public function getLink(){
    global $site;
    return $site->generateUrl(null, $this->zoneName);
  }
  
  public function getDepth(){
    return 1;
  } 
  
  public function getButtonTitle(){
    global $parametersMod;
    return $parametersMod->getValue('administrator', 'search', 'translations', 'search');
  }
  
  
  public static function compareRank($element1, $element2){
    if($element1->searchRank > $element2->searchRank)
      return -1;
    if($element1->searchRank < $element2->searchRank)
      return 1;
      
    return 0; //equal
  } 
  
	public function generateContent(){
		global $site;
		global $parametersMod;

    $site->requireTemplate('administrator/search/template.php');

		if($this->getId() == null)
      return Template::noSearchString($parametersMod->getValue('administrator', 'search', 'translations', 'search'), $parametersMod->getValue('administrator', 'search', 'translations', 'no_search_word'));
			
			
			
		$searchableZones = explode("\n", $parametersMod->getValue('administrator', 'search', 'options', 'searchable_zones'));
		$combinedZones = explode("\n", $parametersMod->getValue('administrator', 'search', 'options', 'combined_zones'));
		
		$foundElements = array();
		$foundElementsCombined = array();

		$searchWords = explode(" ", $site->getVars['q']);
		foreach ($searchableZones as $key => $value) {
			if ($value != '') {
			  $tmpElements = $site->getZone($value)->getAllElements();
			  $tmpFoundElements = $this->search($site->getZone($value), $tmpElements, $searchWords);
        
        $combined = false;
        foreach ($combinedZones as $combKey => $combinedValue) {
          if ($combinedValue == $value) {
            $combined = true;
          }
        }
        
				if ($combined)
				  $foundElementsCombined = array_merge($foundElementsCombined, $tmpFoundElements);
				else {
				  if(sizeof($tmpFoundElements) > 0){
				    $foundElements[$value] = $tmpFoundElements;
				  }
				}
				 
			}
		}
		$answer = '';
		usort($foundElementsCombined, 'Modules\administrator\search\Element::compareRank');
		
		
		if(sizeof($foundElements) > 0 || sizeof($foundElementsCombined) > 0)
		  return Template::searchResult($parametersMod->getValue('administrator', 'search', 'translations', 'search'), $foundElementsCombined, $foundElements);
		else
		  return Template::noResults($parametersMod->getValue('administrator', 'search', 'translations', 'search'), $parametersMod->getValue('administrator', 'search', 'translations', 'no_results'));
	}
	
	
	
	private function search($zone, $tmpElements, $searchWords, $curDepth = 1){
	  $foundElements = array();
		foreach ($tmpElements as $key => $element) {
		  if ($element->getType() == 'default') {
  			$searchRank = $this->getSearchRank($element, $searchWords);
  			if($searchRank > 0){
  				$element->searchRank = $searchRank;
  				$foundElements[] = $element;
  			}
		  }
		}
		return $foundElements;
	}
	
	private function getSearchRank($element, $searchWords){
	  
	  if($element->getType() == 'inactive' || $element->getType() == 'subpage' || $element->getType() == 'redirect')
	   return 0;
	  
		$match = true;
	  $textRank = 1;
	  $buttonTitleRank = 1;
	  $pageTitleRank = 1;
	  $keywordsRank = 1;
	  $descriptionRank = 1;
		foreach($searchWords as $key => $searchWord){
			$position = mb_stripos($element->getText(), $searchWord);
			if( $position === false) {
				$textRank = 0;	
			}
			
			$position = mb_stripos($element->getButtonTitle(), $searchWord);
			if($position === false) {
				$buttonTitleRank = 0;
			 }	
			
			$position = mb_stripos($element->getPageTitle(), $searchWord);
			if($position === false) {
				$pageTitleRank = 0;
			}

			$position = mb_stripos($element->getKeywords(), $searchWord);
			if($position === false) {
				$keywordsRank = 0;
			}

			$position = mb_stripos($element->getDescription(), $searchWord);
			if($position === false) {
				$descriptionRank = 0;
			}

		}
		return $pageTitleRank*0.6 + $textRank*0.2 + $buttonTitleRank*0.17 + $keywordsRank*0.02 + $descriptionRank*0.01;
	}	
	
	public function generateManagement(){
		return $this->generateContent();
	}  
}




 