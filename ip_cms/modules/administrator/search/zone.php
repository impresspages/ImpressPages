<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\search;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


require_once('element.php');


class Zone extends \Frontend\Zone{


  /**
   * Find elements of this zone.      
   * @return array Element   
   */
  public function getElements($language = null, $parentElementId = null, $startFrom = 1, $limit = null, $includeHidden = false, $reverseOrder = null){
    return array();
  }
  

  /**
   * @param int $elementId       
   * @return Element   
   */
	public function getElement($elementId){
    return new Element(null, $this->name); //default zone return element with all url and get variable combinations
	}
	
	
  /**
   * @param array $url_vars        
   * @return array element   
   */	
  public function findElement($urlVars, $getVars){
	 /*this zone never returns error404 and in reality have no pages (elements)*/
	 if(isset($getVars['q']) && trim($getVars['q'] != '')){
	   return new Element(trim($getVars['q']), $this->name);
	 }else{
	   return new Element(null, $this->name);
	 }
  }
  
  
  public function makeActions(){
		global $site;
		global $log;

	  $searchZone = $site->getZone($this->name);
		if(isset($_POST['action']) && $_POST['action'] == 'search'){
		  $log->log('administrator/search', 'search', $_POST['q']);
			header("location: ".str_replace('&amp;', '&', $site->generateUrl(null, $searchZone->getName(), null, array("q" => $_POST['q']))));
		}
  }
  
	/**
	 * Generate search field
	 * @return string html search form.	 
	 */   	
	public function generateSearchBox(){
		global $site;
		global $parametersMod;
		
		$site->requireTemplate('administrator/search/template.php');
		
		$value = '';
		
		$searchZone = $site->getZoneByModule('administrator', 'search');
		if($site->currentZone == $this->name && isset($_GET['q']))
			$value = $site->getVars['q'];
		
		return Template::searchForm($parametersMod->getValue('administrator','search','translations','search'), $value, $parametersMod->getValue('administrator','search','translations','search'), $site->generateUrl(null, $this->name));
	}
	
}
