<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
 
namespace Modules\administrator\sitemap;

 
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
    return $parametersMod->getValue('administrator', 'sitemap', 'translations', 'sitemap');
  }
  
  
  
	function generateContent(){
		global $site;
		global $parametersMod;
			
 		$site->requireTemplate('administrator/sitemap/template.php');

		$mappedZones = explode("\n", $parametersMod->getValue('administrator', 'sitemap', 'options', 'associated_zones'));
		$mappedZonesDepth = array();
		
		for($i=0; $i<sizeof($mappedZones); $i++){
		  $begin = strrpos($mappedZones[$i], '[');
      $end =  strrpos($mappedZones[$i], ']');
		  if($begin !== false && $end === strlen($mappedZones[$i]) - 1){
		    $mappedZonesDepth[$i] = substr($mappedZones[$i], $begin + 1, - 1);
		    $mappedZones[$i] = substr($mappedZones[$i], 0, $begin);
		  }else
		    $mappedZonesDepth[$i] = -1; //unlimited depth
		}
		
		
		
		$foundElements = array();

		$sitemapHtml = '';
		foreach($mappedZones as $key => $zone){
			if($zone != ''){
			   if($mappedZonesDepth[$key] == -1 ) //unlimited depth
				    $tmpElements = $site->getZone($zone)->getElements();
				 else
				    $tmpElements = $site->getZone($zone)->getElements($mappedZonesDepth[$key]);
        if($tmpElements){
          $sitemapHtml .= Template::zone($site->getZone($zone), $tmpElements);
        }
			}
		}
		
		return Template::sitemap($parametersMod->getValue('administrator', 'sitemap', 'translations', 'sitemap'), $sitemapHtml);
	}
	

	

	

	public function generateManagement(){
		return $this->generateContent();
	}  
}




 