<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Frontend;
 
if (!defined('CMS')) exit;  

require_once (__DIR__.'/element.php');


/**
 *   
 * @package ImpressPages
 */ 
abstract class Zone{
  
	protected $name;
	protected $template;
	protected $current;
	protected $title;
	protected $url;
	protected $keywords;
	protected $description;
	protected $associatedModuleGroup;
	protected $associatedModule;
	
	
	protected $currentElement;
	protected $breadcrumb;


  public function __construct($parameters){
    $this->name = $parameters['name'];
    $this->template = $parameters['template'];
    $this->associatedModuleGroup = $parameters['associated_group'];
    $this->assocaitedModule = $parameters['associated_module'];
  }

  /**
   * Find elements of this zone.      
   * @return array Element   
   */
  public abstract function getElements($language = null, $parentElementId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false);
  

  /**
   * @param int $elementId       
   * @return Element   
   */
	public abstract function getElement($elementId);
	
	
  /**
   * @param array $urlVars        
   * @param array $getVars        
   * @return Element   
   */	
  public abstract function findElement($urlVars, $getVars);
  

	

	/**
		@return array of elements road to current element
	*/
	public function getRoadToElement($elementId=null){

	  
		$elements = array();
		if($elementId !== null)
			$element = $this->getElement($elementId);
		else
			$element = $this->getCurrentElement();
			
    if($element){
      $elements[] = $element;		
      $parentElementId = $element->getParentId();
      while($parentElementId){
        $parentElement = $this->getElement($parentElementId); 
        $elements[] = $parentElement;
        $parentElementId = $parentElement->getParentId();
      }
        
    }
		return array_reverse($elements);			
	}

  /**
   * finds current (active) page of this zone. Calculated value is cached  
   * @return Element   
   */
	public function getCurrentElement(){
		global $site;
		if($this->currentElement !== null){
			return $this->currentElement;
		}
		if($this->name != $site->currentZone){
			$this->currentElement = null;
			return null;
		}
		

    		
		
		$this->currentElement = $this->findElement($site->urlVars, $site->getVars);
		return $this->currentElement;	
	}

  /**
   * finds and returns all elements of that zone  
   * @return array Element   
   */	
	public function getAllElements($languageId = null, $parentId = null){
	  $elements = $this->getElements($languageId, $parentId);
	  $tmpElements = array();
	  foreach($elements as $key => $element){
	   $tmpElements = array_merge($tmpElements, $this->getAllElements($languageId, $element->getId()));
	  }
	  $answer = array_merge($elements, $tmpElements);
	  return $answer;
	}
	
  /**
   * executes ajax or other actions required by module  
   */	
	public function makeActions(){
	}	

  public function getName(){return $this->name;}
  public function setName($name){$this->name=$name;}
  
  public function getTemplate(){return $this->template;}
  public function setTemplate($template){$this->template=$template;}
  
  public function getTitle(){return $this->title;}
  public function setTitle($title){$this->title=$title;}

  public function getUrl(){return $this->url;}
  public function setUrl($url){$this->url=$url;}

  public function getKeywords(){return $this->keywords;}
  public function setKeywords($keywords){$this->keywords=$keywords;}

  public function getDescription(){return $this->description;}
  public function setDescription($description){$this->inactiveIfParent=$description;}

  public function getAssociatedModuleGroup(){return $this->associatedModuleGroup;}
  public function setAssociatedModuleGroup($associatedModuleGroup){$this->associatedModuleGroup=$associatedModuleGroup;}

  public function getAssociatedModule(){return $this->associatedModule;}
  public function setAssociatedModule($associatedModule){$this->associatedModule=$associatedModule;}
  
  public function getBreadcrumb(){
    global $site;
    if ($site->currentZone == $this->name) {
      if ($this->breadcrumb) {
        return $this->breadcrumb;
      } else {
        if($this->getCurrentElement()){
          $this->breadcrumb = $this->getRoadToElement($this->getCurrentElement()->getId());
          return $this->breadcrumb;
        } else {
          return array();
        }      
      }
    }
    return array();  
  }
  
}


