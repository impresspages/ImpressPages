<?php
/**
 *
 * ImpressPages CMS dynamic sitemap
 * 
 * This file generates sitemap index and sitemaps.
 * 
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

/** @private */

/** @private */


define('SITEMAP_MAX_LENGTH', 600);

define('CMS', true);
define('FRONTEND', true);
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');

require ('ip_config.php');
require (INCLUDE_DIR.'parameters.php');
require (INCLUDE_DIR.'db.php');

require (FRONTEND_DIR.'db.php');
require (FRONTEND_DIR.'site.php');
require (FRONTEND_DIR.'session.php');
require (MODULE_DIR.'administrator/log/module.php'); 
require (BASE_DIR.INCLUDE_DIR.'error_handler.php');


if(\Db::connect()){
	$log = new \Modules\administrator\log\Module();

  $parametersMod = new ParametersMod();
  $session = new \Frontend\Session();
  
  
  $site = new \Frontend\Site();
  $site->configZones();


  $sitemap = new Sitemap();  
    
  if(isset($_GET['nr']) && isset($_GET['zone'])){
    echo $sitemap->getSitemap($_GET['zone'], $_GET['nr']);
  }else{
    echo $sitemap->getSitemapIndex();  
  }


  \Db::disconnect();  
}else   trigger_error('Database access');
     
     
     
/**
 * Sitemap index and sitemap generation class
 * @package ImpressPages
 */     
class sitemap{
  var $mappedZones;
  
  function __construct(){
    global $parametersMod;
    $this->mappedZones = array();
		$mappedZones = explode("\n", $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'xml_sitemap_associated_zones'));

  	$mapped_zone = null;
		for($i=0; $i<sizeof($mappedZones); $i++){
		  $begin = strrpos($mappedZones[$i], '[');
      $end =  strrpos($mappedZones[$i], ']');
		  if($begin !== false && $end === strlen($mappedZones[$i]) - 1){
		    $tmp_name = substr($mappedZones[$i], 0, $begin);
		    $this->mappedZones[$tmp_name] = substr($mappedZones[$i], $begin + 1, - 1);
		  }else{
		    $this->mappedZones[$mappedZones[$i]] = -1;
		  }
      
		}  	
  	
  }
  
  /**
   * Generates sitemap XML
   * @param int $nr Number of sitemap. Big sites are split into several sitemaps. Begining from 0.
   * @return string Sitemap XML      
   */
  function getSitemap($zone,$nr){
    global $parametersMod;
    global $site;

  	if (!isset($this->mappedZones[$zone]) || $site->getZone($zone) == false) {
     header('HTTP/1.0 404 Not Found');
     \Db::disconnect();
  	 exit;
  	}
  
  
    header('Content-type: application/xml; charset="'.CHARSET.'"',true);
  	


  	$answer = '';
  	$answer .= '<'.'?xml version="1.0" encoding="'.CHARSET.'"?'.'>
  		<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  	
  	';
  	
  	
  	
  	if($this->mappedZones[$zone] == -1) //unlimited depth
  	 $pages = $this->getPages($site->getZone($zone));
  	else
  	 $pages = $this->getPages($site->getZone($zone), $this->mappedZones[$zone]);
  	
  	
  	for($i=$nr*SITEMAP_MAX_LENGTH; $i<($nr+1)*SITEMAP_MAX_LENGTH; $i++){
  		if(isset($pages[$i])){
  			$answer .= '
  			   <url>
  			      <loc>'.$pages[$i]->getLink().'</loc>
  			';
  			if ($pages[$i]->getLastModified()) { 
    			$answer .= '<lastmod>'.substr($pages[$i]->getLastModified(), 0, 10).'</lastmod>
    			';
  			}
  			if($frequency = $pages[$i]->getModifyFrequency()){
  				$tmp_freq = '';
  				if($frequency < 60*30) //30 min
  					$tmp_freq = 'always';
  				elseif($frequency < 60*60) //1 hour
  					$tmp_freq = 'hourly';
  				elseif($frequency < 60*60*24) //1 day
  					$tmp_freq = 'daily';
  				elseif($frequency < 60*60*24*7) //1 week 
  					$tmp_freq = 'weekly';
  				elseif($frequency < 60*60*24*30) //1 month
  					$tmp_freq = 'monthly';
  				elseif($frequency < 60*60*24*360*2) //2 years
  					$tmp_freq = 'yearly';
  				else
  					$tmp_freq = 'never';
  				
  					
  				$answer .= '<changefreq>'.$tmp_freq.'</changefreq>
  				';
  			}
  			if ($tmpPriority = $pages[$i]->getPriority()) {
    			$answer .= '<priority>'.$tmpPriority.'</priority>
    			';
  			}
  			$answer .= '
  			   </url>
  			';
  		}
  		
  	}
  	
  	$answer .= '
  	</urlset>';
  	return $answer;
  }	 
  	 
  	 
  /**
   * Generates array of all website pages    
   * @return array ('link', 'last_modified', 'modify_frequency', 'priority')    
   */  	 
  function getPages($zone, $maxDepth = 1000, $parentId = null, $curDepth = 1){
  	global $site;
  	$pages = array();
  	if ($curDepth <= $maxDepth) {
    	foreach($site->languages as $key => $language){
    	  $tmpElements = $zone->getElements($language['id'], $parentId);
    	  foreach ($tmpElements as $key => $element) {
    	    if ($element->getType() == 'default') {
    	      $pages[] = $element;
    	    }
    	    $pages = array_merge($pages, $this->getPages($zone, $maxDepth, $element->getId(), $curDepth+1));
    	  }
    	}
  	}
    return $pages;
  }
  
  
  /**
   * @param array &$pages elements with aditional values for sitemap
   * @param array $elements standard website zone elements array
   * @param int $maxDepth maximal elements tree depth
   * @param int $inactiveDepth depth of inactive elements
   * @param bool $inactiveIfParent element is inactive if have a children
   * @param int $depth current depth in recursion                
   * @return array    
   */  	 
  /*function getElements(&$pages, $elements, $maxDepth, $inactiveDepth, $inactiveIfParent, $depth = 1){
    if($depth <= $maxDepth){
  	if(is_array($elements)){
  		foreach($elements as $key => $element){
  			if($depth > $inactiveDepth &&
  				(!($inactiveIfParent && sizeof($element['childs'])>0))
  			){
  			
  				$sitemap_encode1 = array("&", "'", '"', '>', '<');
  				$sitemap_encode2   = array("&amp;", "&apos;", "&quot;", "&gt;", "&lt;");
  				$pages[]['link'] = str_replace($sitemap_encode1, $sitemap_encode2, ($element['link']));
  				$pages[(sizeof($pages) - 1)]['last_modified'] = $element['last_modified'];
  				$pages[(sizeof($pages) - 1)]['modify_frequency'] = $element['modify_frequency'];
  				$pages[(sizeof($pages) - 1)]['priority'] = $element['priority'];
  			}
  			if($maxDepth > $depth && sizeof($element['childs']) > 0 ){
  				$this->getElements($pages, $element['childs'], $maxDepth, $inactiveDepth, $inactiveIfParent, $depth+1);
  			}
  		}
  	}
    
    }
    return $pages;
  }*/
  
  	 
  /**
   * @return string sitemap index XML    
   */  	  	 
  function getSitemapIndex(){
    global $site;
    
    header('Content-type: application/xml; charset="'.CHARSET.'"',true);
    
    $answer = ''; 

    $answer .= '<'.'?xml version="1.0" encoding="'.CHARSET.'"?'.'>
  <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  
    foreach($this->mappedZones as $curZone => $curDepth){
      if($curDepth == -1) //unlimited depth
        $count = $this->getPagesCount($site->getZone($curZone));
      else
        $count = $this->getPagesCount($site->getZone($curZone), $curDepth);
      for($i=0; $i<$count/SITEMAP_MAX_LENGTH; $i++){
        $answer .= '
       <sitemap>
          <loc>'.BASE_URL.'sitemap.php?zone='.$curZone.'&amp;nr='.$i.'</loc>	  
       </sitemap>
        ';  
      
      }
    }     
    
    $answer .= '</sitemapindex>
    ';
    return $answer;
  }
  
  
  /**
   * @return int active and visible pages count in all zones
   */  	  
  function getPagesCount($zone, $maxDepth = 1000, $parentId = null, $curDepth = 1){
  	global $site;
  	$count = 0;
  	if ($curDepth <= $maxDepth) {
    	foreach($site->languages as $key => $language){
    	  $tmpElements = $zone->getElements($language['id'], $parentId);
    	  foreach ($tmpElements as $key => $element) {
    	    if ($element->getType() == 'default') {
    	      $count++;
    	    }
    	    $count += $this->getPagesCount($zone, $maxDepth, $element->getId(), $curDepth+1);
    	  }
    	}
  	}
    return $count;
  }
  
  
  
  /**
   * @param array $elements standard website zone elements array
   * @param int $maxDepth maximal elements tree depth
   * @param int $inactiveDepth depth of inactive elements
   * @param bool $inactiveIfParent element is inactive if have a children
   * @param int $depth current depth in recursion         
   * @return int active and visible pages count from given elements
   */  	  
  /*function getElementsCount($elements, $maxDepth, $inactiveDepth, $inactiveIfParent, $depth = 1){
    $count = 0;
    if($depth <= $maxDepth){
  	if(is_array($elements)){
  		if($depth > $inactiveDepth)
  			$count = $count + count($elements);
  		foreach($elements as $key => $element){
  			if($maxDepth > $depth && isset($element['childs']) && sizeof($element['childs']) > 0 ){
  				if($inactiveIfParent && $depth > $inactiveDepth)
  					$count--;
  				$count = $count + $this->getElementsCount($element['childs'], $maxDepth, $inactiveDepth, $inactiveIfParent, $depth+1);
  			}
  		}
  	}
    
    }
    return $count;
  }*/

}





?>