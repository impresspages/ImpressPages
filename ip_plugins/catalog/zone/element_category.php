<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
 
namespace Modules\catalog\zone;

 
if (!defined('CMS')) exit;  


/**
 * Website zone element. Typically each element represents one page on zone.<br />
 *   
 * @package ImpressPages
 */ 

class ElementCategory extends \Frontend\Element{

  protected $photo;

  public function getLink(){
    if($this->link == null){
      $this->generateDepthAndLink();
    }  
    
    return $this->link;
  }
  
  public function getDepth(){
    if($this->depth == null)
      $this->generateDepthAndLink();
    
    return $this->depth;
  } 
  
  
  public function generateContent(){    
    require_once(__DIR__.'/template.php');
    global $site;
    global $parametersMod;
    
    $answer = '';

    $zone = $site->getZone($this->getZoneName());
    $subCategories = $zone->getElements(null, $this->getId(), 0, null, false, true, false);
    $elements = $zone->getElements(null, $this->getId(), 0, null, false, false, true);
    $itemsPerPage = (int)$parametersMod->getValue('catalog', 'zone', 'options', 'items_per_page');
    if(isset($site->getVars['page'])){
      $currentPage = (int)$site->getVars['page'];
      if($currentPage < 1){
        $currentpage = 1;
      }
    } else {
      $currentPage = 1;
    }
    
    $pagesCount = ceil(sizeof($elements) / $itemsPerPage);   
    $pages = array();
    for($i=1; $i<=$pagesCount; $i++){
      $pages[] = $this->getLink().'?page='.$i;
    }
    $elements = array_slice($elements, ($currentPage-1)*$itemsPerPage, $itemsPerPage);
    
    
    $answer .= Template::generateList($this, $subCategories, $elements, $pages, $currentPage);
    return $answer;
  }
  
  
  
  
  
  public function generateManagement(){
    $this->generateContent();           
  }

  private function generateDepthAndLink(){
    global $site;
    
    if($this->getId() === null){
      $this->depth = 1;
      $this->link = $site->generateUrl(null, $this->zoneName);
    } else {
      $tmpUrlVars = array();
      $dbItem = \Modules\catalog\categories\Db::getCategoryById($site->currentLanguage['id'], (int)($this->getId()/10));
      $parentId = $dbItem['parent_id'];
      $tmpUrlVars[] = $dbItem['url'];
      
      while($parentId !== null)
      {
        $dbItem = \Modules\catalog\categories\Db::getCategoryById($site->currentLanguage['id'], $parentId);
        $tmpUrlVars[] = $dbItem['url'];
        $parentId = $dbItem['parent_id'];             
      }
      
      $urlVars = array_reverse($tmpUrlVars);
      
      $this->depth = sizeof($urlVars);
      
      $this->link = $site->generateUrl(null, $this->zoneName, $urlVars);
    }
  }
  
  public function setPhoto($photo){
    $this->photo = $photo;
  }
  
  public function getPhoto(){
    return $this->photo;
  }
}




