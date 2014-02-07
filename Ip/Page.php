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
 * This class is responsible for generating a content and providing with information about a web page.
 *
 */

class Page{
    /** int - unique number of element in that zone. */
    protected $id;
    /** string - title that will be placed in menu on the link to this page */
    protected $navigationTitle;
    /** string - part of link. Identifies actual page */
    protected $url;

    protected $languageCode;

    protected $slug;

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

    public function __construct($id)
    {
        $this->id = $id;

        $page = ipDb()->selectRow('page', '*', array('id' => $id));

        if (!$page) {
            throw new \Ip\Exception("Page #{$id} not found.");
        }

        foreach ($page as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * This function is being executed every time before this page element is being displayed.
     *
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
     * Get the next page on website's page tree branch
     *
     * @return Page or false if next element doesn't exist
     */
    public function getNextPage()
    {
        if($this->nextElement === null){
            $this->findPreviousAndNextElements();
        }
        return $this->nextElement;
    }

    /**
     * Get previous page on website's page tree branch
     *
     * @return Page Page object or false if previous element doesn't exist
     */
    public function getPreviousPage()
    {
        if($this->previousElement === null){
            $this->findPreviousAndNextElements();
        }
        return $this->previousElement;
    }

    /**
     * Get page ID
     *
     * @return int Page ID
     */
    public function getId(){return $this->id;}

    /**
     * Set page ID
     *
     * @ignore
     * @param $id int
     */
    public function setId($id){$this->id = $id;}

    /**
     * Get page navigation title
     *
     * @return string A title for page navigation
     */
    public function getNavigationTitle(){return $this->navigationTitle;}

    /**
     * @ignore
     *
     * @param $navigationTitle string
     */
    public function setNavigationTitle($navigationTitle){$this->navigationTitle= $navigationTitle;}

    /**
     * Get page title
     *
     * @return string Page title
     */
    public function getPageTitle(){return $this->pageTitle;}

    /**
     * Set page title
     *
     * @ignore
     * @param $pageTitle string
     */
    public function setPageTitle($pageTitle){$this->pageTitle = $pageTitle;}

    /**
     * Get page keywords
     *
     * @return string Page keywords
     */
    public function getKeywords(){return $this->keywords;}

    /**
     * Set keywords
     *
     * @ignore
     * @param $keywords string
     */
    public function setKeywords($keywords){$this->keywords=$keywords;}

    /**
     * Get page description
     *
     * @return string Page description text
     */
    public function getDescription(){return $this->description;}

    /**
     * @ignore
     * @param $description string
     */
    public function setDescription($description){ $this->description=$description;}

    /**
     * @ignore
     * @return string Get page text
     */
    public function getText(){return $this->text;}

    /**
     * @ignore
     * @param $text string
     */
    public function setText($text){$this->text=$text;}

    /**
     * Get page modification date and time
     *
     * @return string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS'
     */
    public function getLastModified(){return $this->lastModified;}

    /**
     * Set page modification date and time
     *
     * @ignore
     * @param $lastModified string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS'
     */
    public function setLastModified($lastModified){$this->lastModified=$lastModified;}

    /**
     * Get page creation date and time
     *
     * @return string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS'
     */
    public function getCreatedOn(){return $this->createdOn;}

    /**
     * Set page creation date and time
     *
     * @param $createdOn string in MySql timestamp format 'YYYY-MM-DD HH:MM:SS'
     */
    public function setCreatedOn($createdOn){$this->createdOn=$createdOn;}

    /**
     * @ignore
     * @param $modifyFrequency int represents average amount of days between changes
     */
    public function setModifyFrequency($modifyFrequency){$this->modifyFrequency=$modifyFrequency;}

    /**
     * @ignore
     * @return float
     */
    public function getPriority(){return $this->priority;}

    /**
     * @ignore
     * @param $priority float
     */
    public function setPriority($priority){$this->priority=$priority;}

    /**
     * Get parent page ID
     *
     * @return int Parent page ID
     */
    public function getParentId(){return $this->parentId;}

    /**
     * Set parent page id
     *
     * @ignore
     * @param $parentId int
     */
    public function setParentId($parentId){$this->parentId=$parentId;}

    /**
     * Get page URL address
     * @return string Page URL
     */
    public function getLink()
    {
        return $this->languageCode . '/' . $this->url;
    }

    /**
     * @ignore
     * @param $link string
     */
    public function setLink($link){$this->link=$link;}

    /**
     * Get the last part of the page URL
     * @return string Page URL
     */
    public function getUrl(){return $this->url;}

    /**
     * @ignore
     * @param $url string
     */
    public function setUrl($url){$this->url=$url;}

    /**
     * Check if the page is the currently opened page in the browser
     *
     * @return bool True, if the page is a current page
     */
    public function isCurrent(){return $this->current;}

    /**
     * Set the page as currently opened in the browser
     *
     * @ignore
     * @param $current bool
     */
    public function setCurrent($current){$this->current=$current;}

    /**
     * Check if the page exists in current breadcrumb
     *
     * @return bool True, if the page is in a current breadcrumb
     */
    public function isInCurrentBreadcrumb(){return $this->selected;}

    /**
     * Set the page as existing in current breadcrumb
     * @ignore
     * @param $selected bool
     */
    public function markAsInCurrentBreadcrumb($selected){$this->selected=$selected;}

    /**
     * Get page depth level in a menu tree
     *
     * @return int Depth level.
     */
    public function getDepth(){return $this->depth;}

    /**
     * Set page depth level in a menu tree
     * @ignore
     *
     * @param $depth int Depth level
     */
    public function setDepth($depth){$this->depth=$depth;}

    /**
     * Get zone name of the page
     *
     * @return string Zone name
     */
    public function getZoneName(){return $this->zoneName;}

    /**
     * @ignore
     *
     * @param $zoneName string
     */
    public function setZoneName($zoneName){$this->zoneName=$zoneName;}

    /**
     * Get the page type (e.g., default, redirect or other types)
     *
     * @return string Page type
     */
    public function getType(){return $this->type;}

    /**
     * Set the page type
     * @ignore
     *
     * @param $type string Page type
     */
    public function setType($type){$this->type=$type;}

    /**
     *
     * Get page redirect address URL
     *
     * @return string Redirect URL address
     */
    public function getRedirectUrl(){return $this->redirectUrl;}

    /**
     * Set page redirect URL
     *
     * @ignore
     * @param $redirectUrl string Redirect URL address
     */
    public function setRedirectUrl($redirectUrl){$this->redirectUrl=$redirectUrl;}

    /**
     * Get page visibility status
     *
     * @return bool Visibility
     */
    public function isVisible(){return $this->visible;}

    /**
     * Set page visibility status
     *
     * @ignore
     * @param $visible bool
     */
    public function setVisible($visible){$this->visible=$visible;}

    public static function createList($list)
    {
        $pages = array();
        foreach ($list as $page) {
            $pages[]= new \Ip\Page($page['id']);
        }

        return $pages;
    }
}
