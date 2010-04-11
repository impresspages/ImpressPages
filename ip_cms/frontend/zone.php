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
 *  ImpressPages system doesn't manage separate pages by itself.
 *  For this task special modules are created that is called "zones".
 *  Each zone may have any number of pages with any content. The pages are the objects, that extends base class Element.
 *  CMS only finds currently required zone (by specified url) and asks its to supply current Element and put the content.
 *  
 *  If you wish to create your own zone of website, extend this class. Overwrite abstract methods and you are done.
 *  Now you have full controll of what is the tree of pages in this zone and how to display the content.
 *  
 *  
 *
 */ 
abstract class Zone{
  
  /** string Zone name */
	protected $name;
	 
  /** string Layout file */
	protected $layout;
	
  /** string Default zone title */
	protected $title;
	
  /** string Zone url */
	protected $url;
	
	/** string Default zone keywords */
	protected $keywords;
	
  /** string Default zone description */
  protected $description;
  
	/** string */
	protected $associatedModuleGroup;
	
  /** string */
	protected $associatedModule;
	
  /** Element - Once it is found, it is stored for future use. */
	protected $currentElement;
	
  /** array of Elements (Element). Once the breadcrumb is generated, it is stored for future use.  */
	protected $breadcrumb;


  public function __construct($parameters){
    $this->name = $parameters['name'];
    $this->layout = $parameters['template'];
    $this->associatedModuleGroup = $parameters['associated_group'];
    $this->assocaitedModule = $parameters['associated_module'];
  }

  /**
   * 
   * Find elements of this zone. This function should return only one level of menu tree.
   * If $parentElementId is null, then function returns the first level of elements. 
   * Contrarily - child elements of specified parent Element.
   * 
   * @param $language Language id. If not set, current website language is used.
   * @param $parentElementId if set, return only childs of specified parent
   * @param $startFrom MySql syntax to limit returning elements count.
   * @param $limit MySql syntax to limit returning elements count.
   * @param $includeHidden set to false if you need only visible elements (some elements might be temporary hidden).
   * @param $reverseOrder return elements in reverse order. Might be usefull when floating elements to right.
   * @return array Element
   * 
   */
  public abstract function getElements($language = null, $parentElementId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false);
  

  /**
   * 
   * Return element by specified id.
   * 
   * @param $elementId int       
   * @return Element by specified id.
   *    
   */
	public abstract function getElement($elementId);
	
	
  /**
   * 
   * Find element by URL and GET variables. This function is used to find current Element (page) of requested URL.
   * 
   * If requested url is http://yuorsite.com/en/zone_url/var1/var2/?page=2
   * 
   * then
   * 
   * $urlVars == array('var1', 'var2');
   * $getVars == array('page' = 2);
   * 
   * Use these values to detect which of your zone is requested and create required Element (page). 
   *
   *  
   * @param $urlVars array        
   * @param $getVars array        
   * @return Element
   *    
   */	
  public abstract function findElement($urlVars, $getVars);
  

	
  
	/**
	 * 
	 * Get breadcrumb from root to required Element. This function should work by default if you override abstract methods.
	 * You don't need to override it, except in case if you need to change something in it.
	 * 
	 * @param $elementId
	 * @return array of elements - breadcrumb from root to required Element
	 * 
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
      while($parentElementId !== null && $parentElementId !== false){
        $parentElement = $this->getElement($parentElementId); 
        $elements[] = $parentElement;
        $parentElementId = $parentElement->getParentId();
      }
        
    }
		return array_reverse($elements);			
	}

  /**
   * 
   * Finds current (active) page of this zone. Calculated value is cached.
   * 
   * This function should work by default if you override abstract methods.
   * You don't need to override it, except in case if you need to change something in it.
   *   
   * @return Element - element, that represents current requested page.
   *    
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
   * 
   * Finds and returns all elements of that zone
   * 
   * This function should work by default if you override abstract methods.
   * You don't need to override it, except in case if you need to change something in it.
   *    
   * @return array Element
   *    
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
   * 
   * Get breadcrumb to current element. This function should work by default if you override abstract methods.
   * You don't need to override it, except in case if you need to change something in it.   *
   * @return string
   *  
   */
	
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
	
  /**
   * 
   * If you need to do some actions before any output, extend this class.
   * This function is executed by the system allways, when this zone is active (current page depends to this zone).
   * 
   * Also this function is executed if two reserved GET parameters are passed (module_group, module_name) and they are equal to current zone group and name.  
   *  
   * module_group == $this->associatedModuleGroup;
   * and
   * module_name == $this->associatedModule;
   *   
   */	
	public function makeActions(){
	}	

	/** @return string Zone name */
  public function getName(){return $this->name;}
  /** @param $name string */
  public function setName($name){$this->name=$name;}
  
  /** @return string zone layout file */
  public function getLayout(){return $this->layout;}
  /** @param $layout string */
  public function setLayout($layout){$this->layout=$layout;}
  
  /** @return string Default title */
  public function getTitle(){return $this->title;}
  /** @param $title string */
  public function setTitle($title){$this->title=$title;}

  /** @return string Zone URL */
  public function getUrl(){return $this->url;}
  /** @param $url string */
  public function setUrl($url){$this->url=$url;}

  /** @return string Default keywords */
  public function getKeywords(){return $this->keywords;}
  /** @param $keywords string */
  public function setKeywords($keywords){$this->keywords=$keywords;}

  /** @return string Default description */
  public function getDescription(){return $this->description;}
  /** @param $description string */
  public function setDescription($description){$this->inactiveIfParent=$description;}

  /** @return string */
  public function getAssociatedModuleGroup(){return $this->associatedModuleGroup;}
  /** @param $associatedModuleGroup string */
  public function setAssociatedModuleGroup($associatedModuleGroup){$this->associatedModuleGroup=$associatedModuleGroup;}

  /** @return string */
  public function getAssociatedModule(){return $this->associatedModule;}
  /** @param $associatedModule string */
  public function setAssociatedModule($associatedModule){$this->associatedModule=$associatedModule;}

  
}


