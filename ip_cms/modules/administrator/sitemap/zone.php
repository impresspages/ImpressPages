<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\administrator\sitemap;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


require_once (__DIR__.'/element.php');

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
   * @param array $urlVars        
   * @param array $getVars        
   * @return Element or false if page does not exist   
   */	
  public function findElement($urlVars, $getVars){
    if(sizeof($urlVars) == 0)
      return new Element(null, $this->name); //default zone return element with all url and get variable combinations
    else
      return false;
  }
  



	
}
