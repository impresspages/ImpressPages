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

class ElementItem extends \Frontend\Element{

  protected $photo;
  protected $photoBig;
  protected $price;
  protected $discount;
  protected $quantity;
  protected $nextElement;
  protected $previousElement;
  protected $photos;
  protected $content;
  protected $file;

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
    
    $answer = '';
    
    $zone = $site->getZone($this->getZoneName());
    $answer .= Template::generateItem($this);
    return $answer;
  }
  
  public function generateManagement(){
    $this->generateContent();           
  }

  private function generateDepthAndLink(){
    global $site;
    $tmpUrlVars = array();
    
    $dbItem = \Modules\catalog\items\Db::getItemById($site->currentLanguage['id'], (int)($this->getId()/10));
    $parentId = $dbItem['category_id'];
    $tmpUrlVars[] = $dbItem['meta_url'];
    
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
  
  public function getNextElement(){
    $element = parent::getNextElement();
    if($element && $element->getId()%10 === 1){
      return $element;
    } else {
      return false;
    }
  }
  
  public function getPreviousElement(){
    $element = parent::getPreviousElement();
    if($element && $element->getId()%10 === 1){
      return $element;
    } else {
      return false;
    }
  }
  
  public function setContent($content){
    $this->content = $content;
  }
  
  public function getContent(){
    return $this->content;
  }
  
  public function setFile($file){
    $this->file = $file;
  } 
  
  public function getFile(){
    return $this->file;
  }
  
  public function setPhoto($photo){
    $this->photo = $photo;
  }
  
  public function getPhoto(){
    if($this->photo)
      return BASE_URL.IMAGE_DIR.$this->photo;
    else
      return null;
  }
  
  public function setPhotoBig($photo){
    $this->photoBig = $photo;
  }
  
  public function getPhotoBig(){
    if($this->photoBig)
      return BASE_URL.IMAGE_DIR.$this->photoBig;
    else
      return null;
  }  
  
  public function getPhotos(){
    if($this->photos == null){
      require_once(BASE_DIR.PLUGIN_DIR.'catalog/items/db.php');
      $this->photos = \Modules\catalog\items\Db::getPhotos($this->getId()/10);
      foreach($this->photos as &$photo){
        $photo['photo'] = BASE_URL.IMAGE_DIR.$photo['photo'];
        $photo['photo_big'] = BASE_URL.IMAGE_DIR.$photo['photo_big'];
      }
    }
    return $this->photos; 
  }
  
  public function setPrice($price){
    $this->price = $price;
  }
  
  public function getPrice(){
    return $this->price;
  }
  
  public function setDiscount($discount){
    $this->discount = $discount;
  }
  
  public function getDiscount(){
    return $this->discount;
  }
  
  public function setQuantity($quantity){
    $this->quantity = $quantity;
  }
  
  public function getQuantity(){
    return $this->quantity;
  }
  
  
  
}




