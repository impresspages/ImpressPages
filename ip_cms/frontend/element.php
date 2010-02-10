<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Frontend;
 
if (!defined('CMS')) exit;  



/**
 * Website zone element. Typically each element represents one page on zone.<br />
 *   
 * @package ImpressPages
 */ 
class Element{
  /** @var mixed unique number (or string) of element in that zone.*/
  protected $id;
  /** @var string that should be placed in menu on the link to this page (for eg. in menu)*/
  protected $buttonTitle;
  /** @var string meta tag title*/
  protected $pageTitle;
  /** @var string meta tag keywords*/
  protected $keywords;
  /** @var string meta tag description*/
  protected $description;
  /** @var string html version of page content. Used for search and similar tasks. This field can be used like cache. It isn't the content that will be printed out to the site.*/
  protected $html;
  /** @var string text version of page content. Used for search and similar tasks. This field can be used like cache. It isn't the content that will be printed out to the site.*/
  protected $text;
  /** @var string date when last change in this page was made. MySql timestamp format 'YYYY-MM-DD HH:MM:SS'*/
  protected $lastModified;
  /** @var string page cration date. MySql timestamp format 'YYYY-MM-DD HH:MM:SS'*/
  protected $createdOn;
  /** @var integer, that represents average amount of days between changes*/
  protected $modifyFrequency;
  /** @var boolean 1 if page should be placed on rss feed and 0 if not*/
  protected $rss;
  /** @var float value from 0 to 1, representing importance of page. 0 - lowest importance, 1 - highest importance*/
  protected $priority;
  /** @var integer id of parent or null. Parents can be only elements from the same zone*/
  protected $parentId;
  /** @var string url (including http://) to this page. It chould be generated with global variable $site->generateUrl($languageId=null, $currentZone=null, $urlVars=null, $getVars=null);*/
  protected $link;
  /** @var string part of link. Identifies actual page;*/
  protected $url;
  /** @var bool true if this element is current active page of the site*/
  protected $current;
  /** @var bool true if this element is part of current breadcrumb*/
  protected $selected;
  /** @var int depth of element. Start at 1*/
  protected $depth;
  /** @var string element type:<br/>
   * default - show content
   * inactive - without link on it
   * subpage - redirect to first subpage
   * redirect - redirect to external page
   */
  protected $type;
  /** @var string redirect URL if element type is "redirect" */
  protected $redirectUrl;

  /** @var String zone name of element*/
  protected $zoneName;
  /** @var Boolean*/
  protected $visible;
  
  
  public function generateContent () {
  }

  public function generateManagement () {
  }

  public function __construct($id, $zoneName){    
    $this->id = $id;
    $this->zoneName = $zoneName;
  }
  
  private function findPreviousAndNextElements(){
    global $site;
    $zone = $site->getZone($this->zoneName);
    $elements = $zone->getElements(null, $this->parentId);
    for($i = 0; $i<sizeof($elements); $i++){
      if($elements[$i]->getId() == $this->getId()){
        if(isset($elements[$i-1])){
          $this->previousElement = $elements[$i-1];
        } else{
          $this->previousElement = false;
        }
        if(isset($elements[$i+1])){
          $this->nextElement = $elements[$i+1];
        } else{
          $this->nextElement = false;
        }
      }
    }
  }
  
  /**
   * 
   * @return mixed element object or false if next element does't exist
   */
  public function getNextElement(){
    global $site;
    if($this->nextElement === null){
      $this->findPreviousAndNextElements();
    }
    return $this->nextElement;
  }
  
  /**
   * 
   * @return mixed element object or false if previous element does't exist
   */
  public function getPreviousElement(){
    global $site;
    if($this->previousElement === null){
      $this->findPreviousAndNextElements();
    }
    return $this->previousElement;
  }
  
  
  public function getId(){return $this->id;}
  public function setId($id){$this->id = $id;}

  public function getButtonTitle(){return $this->buttonTitle;}
  public function setButtonTitle($buttonTitle){$this->buttonTitle= $buttonTitle;}

  public function getPageTitle(){return $this->pageTitle;}
  public function setPageTitle($pageTitle){$this->pageTitle = $pageTitle;}

  public function getKeywords(){return $this->keywords;}
  public function setKeywords($keywords){$this->keywords=$keywords;}

  public function getDescription(){return $this->description;}
  public function setDescription($description){ $this->description=$description;}

  public function getHtml(){return $this->html;}
  public function setHtml($html){$this->html=$html;}

  public function getText(){return $this->text;}
  public function setText($text){$this->text=$text;}

  public function getLastModified(){return $this->lastModified;}
  public function setLastModified($lastModified){$this->lastModified=$lastModified;}

  public function getCreatedOn(){return $this->createdOn;}
  public function setCreatedOn($createdOn){$this->createdOn=$createdOn;}

  public function getModifyFrequency(){return $this->modifyFrequency;}
  public function setModifyFrequency($modifyFrequency){$this->modifyFrequency=$modifyFrequency;}

  public function getRss(){return $this->rss; }
  public function setRss($rss){$this->rss=$rss;}

  public function getPriority(){return $this->priority;}
  public function setPriority($priority){$this->priority=$priority;}

  public function getParentId(){return $this->parentId;}
  public function setParentId($parentId){$this->parentId=$parentId;}

  public function getLink(){return $this->link;}
  public function setLink($link){$this->link=$link;}

  public function getUrl(){return $this->url;}
  public function setUrl($url){$this->url=$url;}

  public function getCurrent(){return $this->current;}
  public function setCurrent($current){$this->current=$current;}

  public function getSelected(){return $this->selected;}
  public function setSelected($selected){$this->selected=$selected;}
  
  public function getDepth(){return $this->depth;}
  public function setDepth($depth){$this->depth=$depth;}

  public function getZoneName(){return $this->zoneName;}
  public function setZoneName($zoneName){$this->zoneName=$zoneName;}

  public function getType(){return $this->type;}
  public function setType($type){$this->type=$type;}

  public function getRedirectUrl(){return $this->redirectUrl;}
  public function setRedirectUrl($redirectUrl){$this->redirectUrl=$redirectUrl;}

  public function getVisible(){return $this->visible;}
  public function setVisible($visible){$this->visible=$visible;}
}