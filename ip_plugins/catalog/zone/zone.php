<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\catalog\zone; 

if (!defined('CMS')) exit;



require_once (__DIR__.'/element_category.php');
require_once (__DIR__.'/element_item.php');

require_once (BASE_DIR.PLUGIN_DIR.'catalog/categories/db.php');
require_once (BASE_DIR.PLUGIN_DIR.'catalog/items/db.php');


class Zone extends \Frontend\Zone{

  function __construct($properties){    
    parent::__construct($properties);
  }
  
  

  function getElements($languageId = null, $parentElementId = null, $startFrom = 0, $limit = null, $reverseOrder = false, $includeSubdirectories = true, $includeItems = true){ //includeSubdirectories and includeItems - additional parameters if you wish to get only directories or items 
    global $site;
    global $parametersMod;

    if($languageId == null)
      $languageId = $site->currentLanguage['id'];
    
    $urlVars = array();
    
    if($parentElementId != null){  //if parent specified
      $parentElements = $this->getRoadToElement($parentElementId);
      foreach($parentElements as $key => $element){
        $urlVars[] = $element->getUrl();
      }
    }   

    $breadCrumb = $this->getBreadCrumb();
    $depth = sizeof($urlVars)+1;
    if(isset($breadCrumb[$depth-1])){
      $selectedId = $breadCrumb[$depth-1]->getId();
    }else
      $selectedId = null;
      
    if($parentElementId !== null){ 
      $parentElementId = (int)($parentElementId/10); //last digit specifies category or item
    }

    
    $elements = array();
    //get subdirectories
    if($includeSubdirectories){
      $dbCategories = \Modules\catalog\categories\Db::getCategories($languageId, $parentElementId, $reverseOrder, true, $startFrom, $limit);    
      
      
      foreach($dbCategories as $key => $dbElement){
        $newElement = $this->makeElementFromCategory($dbElement);
        
        if($selectedId == $dbElement['id']*10) // *10 - categories ends with zero
          $newElement->setSelected(1);
        else
          $newElement->setSelected(0);
    
        if($this->currentElement && $this->currentElement->getId() == $dbElement['id']*10) // *10 - categories ends with zero
          $newElement->setCurrent(1);
        else
          $newElement->setCurrent(0);   
        $elements[] = $newElement;
      }
            
    } else {
      $dbCategories = array();
    }
    
    //get items
    if($includeItems){
      if($limit != null){
        $limit = $limit - sizeof($dbCategories);
      }
      if($parametersMod->getValue('catalog', 'zone', 'options', 'show_items_from_subdirectories')){
        $subcategories = $this->getSubcategories($parentElementId);
        $dbItems = array();
        $dbItems = \Modules\catalog\items\Db::getItems($languageId, $parentElementId, $reverseOrder, true, !$parametersMod->getValue('catalog', 'zone','options','show_zero'), sizeof($dbCategories)==0?$startFrom:0, $limit);

        //get subdirecrtories ids
        $ids = array();
        foreach($subcategories as $subcategory){
          $ids[] = $subcategory['id'];
        }
        if(sizeof($ids) > 0){
          $dbItems2 = \Modules\catalog\items\Db::getItems($languageId, $ids, $reverseOrder, true, !$parametersMod->getValue('catalog', 'zone','options','show_zero'), sizeof($dbCategories)==0?$startFrom:0, $limit);
          foreach($dbItems2 as &$item){
            $item['text'] = '';
          }
          $dbItems = array_merge($dbItems, $dbItems2);
        }
        
      } else {
        $dbItems = \Modules\catalog\items\Db::getItems($languageId, $parentElementId, $reverseOrder, true, !$parametersMod->getValue('catalog', 'zone','options','show_zero'), sizeof($dbCategories)==0?$startFrom:0, $limit);
      }
      foreach($dbItems as $key => $dbElement){
        $newElement = $this->makeElementFromItem($dbElement);
        
        if($selectedId == $dbElement['id']*10 + 1) // *10 + 1 - items ends with 1
          $newElement->setSelected(1);
        else
          $newElement->setSelected(0);
    
        if($this->currentElement && $this->currentElement->getId() == $dbElement['id']*10 + 1) // *10 + 1 - items ends with 1
          $newElement->setCurrent(1);
        else
          $newElement->setCurrent(0);   
        $elements[] = $newElement;
      }          
    }

    
    //generate links
    foreach($elements as $key => $element){ //link generation optimization. 
      if($elements[$key]->getType() == 'default')
        $elements[$key]->setLink($site->generateUrl($languageId, $this->getName(), array_merge($urlVars, array($element->getUrl()))));        
    }
    
    return $elements;

  }
  
  function getSubcategories($parentId){
    global $site;
    $answer = array();
    $subcategories = \Modules\catalog\categories\Db::getCategories($site->currentLanguage['id'], $parentId);
    foreach($subcategories as $subcategory){
      $answer[] = $subcategory;
      $answer = array_merge($answer, $this->getSubcategories($subcategory['id']));
    }
    return $answer;
  }

  
  
  function getElement($elementId){
    global $site;
    if ($elementId % 10 == 0){ //category
      $dbElement = \Modules\catalog\categories\Db::getCategoryById($site->currentLanguage['id'], (int)($elementId/10));
      return $this->makeElementFromCategory($dbElement);
    } else { //item
      $dbElement = \Modules\catalog\items\Db::getItemById($site->currentLanguage['id'], (int)($elementId/10));
      return $this->makeElementFromItem($dbElement);
    }
  }

  
  function findElement($urlVars, $getVars){
    global $site;
    $currentEl = null;
    
    if(sizeof($urlVars) == 0){
      $element = false;
      $categories = \Modules\catalog\categories\Db::getCategories($site->currentLanguage['id'], null, false, true, 0, 1);
      
      if(isset($categories[0])){
        return $this->makeElementFromCategory($categories[0]);
      } else {
        return $this->makeRootElement();
        /*$items = \Modules\catalog\items\Db::getItems($site->currentLanguage['id'], null, false, true, 0, 1);
        if(isset($items[0])){
          return $this->makeElementFromItem($items[0]);
        } else{
          return false;
        }*/
      }
    }else{
      $curId = null;
      foreach($urlVars as $key => $value){
        $category = \Modules\catalog\categories\Db::getCategoryByUrl($site->currentLanguage['id'], $value, $curId);
        if($category){
          $curId = $category['id'];
        } else {
          global $parametersMod;          
          if($parametersMod->getValue('catalog', 'zone', 'options', 'show_items_from_subdirectories')){
            //get subcategories
            $subcategories = $this->getSubcategories($curId);
            if($curId == null){
              $item = \Modules\catalog\items\Db::getItemByUrl($site->currentLanguage['id'], $value);
            } else {
              $ids = array($curId);
              foreach($subcategories as $subcategory){
                $ids[] = $subcategory['id'];
              }          
              $item = \Modules\catalog\items\Db::getItemByUrl($site->currentLanguage['id'], $value, $ids);
            }
                
          } else {
            $item = \Modules\catalog\items\Db::getItemByUrl($site->currentLanguage['id'], $value, $curId);
          }
          
          if($item){
            $curId = $item['id'];
          } else {
            return false;
          }
        }
      }
      if($category['id'] == $curId){ //category
        return $this->makeElementFromCategory($category);
      } else {
        return $this->makeElementFromItem($item);
      }
    }
  }  

  private function makeRootElement(){
    $newElement = new ElementCategory(null, $this->getName());
//    $newElement->setButtonTitle($dbElement['button_title']);
//    $newElement->setPageTitle($dbElement['page_title']);
//    $newElement->setKeywords($dbElement['keywords']);
//    $newElement->setDescription($dbElement['description']);
//    $newElement->setUrl($dbElement['url']);
//    $newElement->setPhoto($dbElement['photo']);
//    $newElement->setText($dbElement['text']);
//    $newElement->setLastModified($dbElement['last_modified']);
//    $newElement->setCreatedOn($dbElement['created_on']);
//    $newElement->setModifyFrequency($dbElement['modify_frequency']);
//    $newElement->setRss();

    $newElement->setParentId(null);
    //    $newElement->setHtml();
    $newElement->setType('default');
//    $newElement->setRedirectUrl();

    return $newElement;    
  }
  
  private function makeElementFromCategory($dbElement){
    $newElement = new ElementCategory($dbElement['id'].'0', $this->getName());
    $newElement->setButtonTitle($dbElement['button_title']);
    $newElement->setPageTitle($dbElement['page_title']);
    $newElement->setKeywords($dbElement['keywords']);
    $newElement->setDescription($dbElement['description']);
    $newElement->setUrl($dbElement['url']);
    $newElement->setPhoto($dbElement['photo']);
//    $newElement->setText($dbElement['text']);
    $newElement->setLastModified($dbElement['modified']);
    $newElement->setCreatedOn($dbElement['created']);
//    $newElement->setModifyFrequency($dbElement['modify_frequency']);
//    $newElement->setRss();
    if($dbElement['parent_id'] == null)
      $newElement->setParentId($dbElement['parent_id']);
    else
      $newElement->setParentId($dbElement['parent_id']*10);
    //    $newElement->setHtml();
    $newElement->setType('default');
//    $newElement->setRedirectUrl();

    return $newElement;
  }
  
  private function makeElementFromItem($dbElement){
    $newElement = new ElementItem($dbElement['id'].'1', $this->getName());
    $newElement->setContent($dbElement['description']);
    $newElement->setFile($dbElement['file']);
    $newElement->setButtonTitle($dbElement['title']);
    $newElement->setPageTitle($dbElement['title']);
    $newElement->setKeywords($dbElement['meta_keywords']);
    $newElement->setDescription($dbElement['meta_description']);
    $newElement->setUrl($dbElement['meta_url']);
    $newElement->setText($dbElement['text']);
    $newElement->setLastModified($dbElement['modified']);
    $newElement->setCreatedOn($dbElement['created']);    
//    $newElement->setModifyFrequency($dbElement['modify_frequency']);
//    $newElement->setRss();
    if($dbElement['category_id'] == null){
      $newElement->setParentId($dbElement['category_id']);
    }else{
      $newElement->setParentId($dbElement['category_id']*10);
    }
    $newElement->setHtml($dbElement['html']);
    $newElement->setType('default');
//    $newElement->setRedirectUrl();

    //----------------
    $newElement->setPhoto($dbElement['first_photo']);
    $newElement->setPhotoBig($dbElement['first_photo_big']);
    $newElement->setPrice($dbElement['price']);
    $newElement->setDiscount($dbElement['discount']);
    $newElement->setQuantity($dbElement['quantity']);
    
    return $newElement;
  }
    
  
  
  public function makeActions(){
    if( isset($_REQUEST['action'])) {
      require_once(__DIR__.'/actions.php');
      $actions = new Actions;
      $actions->makeActions();
    }
  }

}
