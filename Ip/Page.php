<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;


/**
 * Handle web page content.
 *
 * A page is a website Zone element. Typically each Element represents a single page on a Zone.
 * * This class is responsible for generating a content and providing with information about a web page.
 *
 */

class Page{
    /** int - unique number of element in that zone. */
    protected $id;
    /** string - title that will be placed in menu on the link to this page */
    protected $navigationTitle;
    /** string - meta tag title */
    protected $pageTitle;
    /** string - meta tag keywords */
    protected $keywords;
    /** string - meta tag description */
    protected $description;
    /** string - html version of page content. Used for search and similar tasks. This field can be used like cache. It isn't the content that will be printed out to the site. */
    protected $html;
    /** string - text version of page content. Used for search and similar tasks. This field can be used like cache. It isn't the content that will be printed out to the site. */
    protected $text;
    /** string - date when last change in this page was made. MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    protected $lastModified;
    /** string - page creation date in MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    protected $createdOn;
    /** integer - average amount of days between changes */
    protected $modifyFrequency;
    /** float - value from 0 to 1, representing importance of page. 0 - lowest importance, 1 - highest importance. Used in XML sitemap. */
    protected $priority;
    /** int - id of parent Element or null. Parents can be only elements from the same zone*/
    protected $parentId;
    /** string - url (including http://) to this page. */
    protected $link;
    /** string - part of link. Identifies actual page */
    protected $url;
    /** bool - true if this element is currently active page */
    protected $current;
    /** bool - true if this element is part of current breadcrumb */
    protected $selected;
    /** int - depth of the element (starts at 1) */
    protected $depth;
    /** string - element type<br />
     * <br />
     * Available values:<br />
     * default - show content<br />
     * inactive - without link on it<br />
     * subpage - redirect to first subpage<br />
     * redirect - redirect to external page<br />
     * error404
     */
    protected $type;
    /** string - redirect URL if element type is "redirect" */
    protected $redirectUrl;

    /** string - zone name of element */
    protected $zoneName;
    /** bool */
    protected $visible;

    /** Element - next sibling element */
    protected $nextElement;
    /** Element - previous sibling element */
    protected $previousElement;

    public function __construct($id, $zoneName)
    {
        $this->id = $id;
        $this->zoneName = $zoneName;
    }


    /**
     *
     * This function returns the content of page. Typically you should use $this->getId() to know which content to output.
     *
     * Override this function to place your code.
     *
     * @return string - page content.
     *
     */
    public function generateContent ()
    {
        $revision = \Ip\ServiceLocator::content()->getCurrentRevision();
        if ($revision) {
            return \Ip\Internal\Content\Model::generateBlock('main', $revision['revisionId'], ipIsManagementState());
        } else {
            return '';
        }
    }


    /**
     * This function is being executed every time before this element page is being displayed.
     * @param \Ip\Controller $controller
     */
    public function init(\Ip\Controller $controller)
    {
        //by default do nothing. Override to do something
    }


    /**
     *
     * Find and cache previous and next elements in elements list.
     *
     */
    private function findPreviousAndNextElements()
    {
        $zone = ipContent()->getZone($this->zoneName);
        $elements = $zone->getPages(null, $this->parentId);
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
     * @return Page or false if next element doesn't exist
     *
     */
    public function getNextElement()
    {
        if($this->nextElement === null){
            $this->findPreviousAndNextElements();
        }
        return $this->nextElement;
    }

    /**
     *
     * @return Page or false if previous element doesn't exist
     *
     */
    public function getPreviousElement()
    {
        if($this->previousElement === null){
            $this->findPreviousAndNextElements();
        }
        return $this->previousElement;
    }

    /** @return int */
    public function getId(){return $this->id;}
    /** @param $id int */
    public function setId($id){$this->id = $id;}

    /** @return string */
    public function getNavigationTitle(){return $this->navigationTitle;}
    /** @param $navigationTitle string */
    public function setNavigationTitle($navigationTitle){$this->navigationTitle= $navigationTitle;}

    /** @return string */
    public function getPageTitle(){return $this->pageTitle;}
    /** @param $pageTitle string */
    public function setPageTitle($pageTitle){$this->pageTitle = $pageTitle;}

    /** @return string */
    public function getKeywords(){return $this->keywords;}
    /** @param $keywords string */
    public function setKeywords($keywords){$this->keywords=$keywords;}

    /** @return string */
    public function getDescription(){return $this->description;}
    /** @param $description string */
    public function setDescription($description){ $this->description=$description;}

    /** @return string */
    public function getHtml(){return $this->html;}
    /** @param $html string */
    public function setHtml($html){$this->html=$html;}

    /** @return string */
    public function getText(){return $this->text;}
    /** @param $text string */
    public function setText($text){$this->text=$text;}

    /** @return string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    public function getLastModified(){return $this->lastModified;}
    /** @param $lastModified string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    public function setLastModified($lastModified){$this->lastModified=$lastModified;}

    /** @return string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    public function getCreatedOn(){return $this->createdOn;}
    /** @param $createdOn string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS' */
    public function setCreatedOn($createdOn){$this->createdOn=$createdOn;}

    /** @return int - average amount of days between changes */
    public function getModifyFrequency(){return $this->modifyFrequency;}
    /** @param $modifyFrequency int represents average amount of days between changes */
    public function setModifyFrequency($modifyFrequency){$this->modifyFrequency=$modifyFrequency;}

    /** @return float */
    public function getPriority(){return $this->priority;}
    /** @param $priority float */
    public function setPriority($priority){$this->priority=$priority;}

    /** @return int */
    public function getParentId(){return $this->parentId;}
    /** @param $parentId int */
    public function setParentId($parentId){$this->parentId=$parentId;}

    /** @return string */
    public function getLink(){return $this->link;}
    /** @param $link string */
    public function setLink($link){$this->link=$link;}

    /** @return string */
    public function getUrl(){return $this->url;}
    /** @param $url string */
    public function setUrl($url){$this->url=$url;}

    /** @return bool */
    public function getCurrent(){return $this->current;}
    /** @param $current bool */
    public function setCurrent($current){$this->current=$current;}

    /** @return bool */
    public function getSelected(){return $this->selected;}
    /** @param $selected bool */
    public function setSelected($selected){$this->selected=$selected;}

    /** @return int */
    public function getDepth(){return $this->depth;}
    /** @param $depth int */
    public function setDepth($depth){$this->depth=$depth;}

    /** @return string */
    public function getZoneName(){return $this->zoneName;}
    /** @param $zoneName string */
    public function setZoneName($zoneName){$this->zoneName=$zoneName;}

    /** @return string */
    public function getType(){return $this->type;}
    /** @param $type string */
    public function setType($type){$this->type=$type;}

    /** @return string */
    public function getRedirectUrl(){return $this->redirectUrl;}
    /** @param $redirectUrl string */
    public function setRedirectUrl($redirectUrl){$this->redirectUrl=$redirectUrl;}

    /** @return bool */
    public function isVisible(){return $this->visible;}
    /** @param $visible bool */
    public function setVisible($visible){$this->visible=$visible;}
}