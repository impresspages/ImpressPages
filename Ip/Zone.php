<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

use Ip\Language;

/**
 *
 *  ImpressPages system doesn't manage separate pages by itself.
 *  For this task special modules called "zones" are created.
 *  Each zone can have any number of pages with any content. Pages are the objects that extend class Page.
 *  CMS only finds currently required zone (by specified url), asks to supply current Page and displays the content.
 *
 *  If you wish to create your own zone of website, extend this class. Overwrite abstract methods and you are done.
 *  Now you have full control of all pages in this zone. It’s up to you how to display the content.
 *
 *
 *
 */
abstract class Zone{

    /** string Zone id */
    protected $id;

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

    /** Page - Once it is found, it is stored for future use. */
    protected $currentPage;

    /** array of Pages (Page). Once the breadcrumb is generated, it is stored for future use.  */
    protected $breadcrumb;

    /** string */
    protected $titleInAdmin;


    public function __construct($parameters){
        $this->name = isset($parameters['name']) ? $parameters['name'] : '';
        $this->layout = isset($parameters['template']) ? $parameters['template'] : '';
        $this->associatedModuleGroup = isset($parameters['associated_group']) ? $parameters['associated_group'] : '';
        $this->associatedModule = isset($parameters['associated_module']) ? $parameters['associated_module'] : '';
        $this->titleInAdmin = isset($parameters['translation']) ? $parameters['translation'] : '';

    }

    /**
     *
     * Finds pages of this zone. This function returns only one level of menu tree.
     * If $parentPageId is null, then function returns the first level of pages.
     * Otherwise, if you specify $parentPageId, then child pages of specified parent Pages is returned.
     *
     * @param $language Language id. If not set, current website language is used.
     * @param $parentPageId if set, function returns only children
     * @param $startFrom MySql syntax to limit returning pages count.
     * @param $limit MySQL syntax to limit the number of pages to return.
     * @param $includeHidden set to false if you need only visible pages (some pages might be temporary hidden).
     * @param $reverseOrder set to true to return pages in reverse order.
     * @return Page[]
     *
     */
    public abstract function getPages($language = null, $parentPageId = null, $startFrom = 0, $limit = null, $includeHidden = false, $reverseOrder = false);


    /**
     *
     * Returns Pages by specified id.
     *
     * @param $pageId int
     * @return Page by specified id.
     *
     */
    public abstract function getPage($pageId);


    /**
     *
     * Finds page by URL and GET variables. This function is used to find current Page (page) of requested URL.
     *
     * If requested url is http://yoursite.com/en/zone_url/var1/var2/?page=2
     *
     * then
     *
     * $urlVars == array('var1', 'var2');
     * $getVars == array('page' = 2);
     *
     * Use these values to detect which of your zone is requested and create required Page.
     *
     *
     * @param $urlVars array
     * @param $getVars array
     * @return Page
     *
     */
    public abstract function findPage($urlVars, $getVars);






    /**
     *
     * Finds current (active) page of this zone. Calculated value is cached.
     *
     * @return Page - that represents current requested page.
     *
     */
    public function getCurrentPage(){
        if($this->currentPage !== null){
            return $this->currentPage;
        }
        $content = \Ip\ServiceLocator::content();
        if(!$content->getCurrentZone() || $this->name != $content->getCurrentZone()->getName()){
            $this->currentPage = false;
            return false;
        }

        $this->currentPage = $this->findPage($content->getUrlVars(), \Ip\ServiceLocator::request()->getQuery());
        return $this->currentPage;
    }

    /**
     *
     * Finds and returns all pages of that zone
     *
     * @return Page[]
     *
     */
    public function getAllPages($languageId = null, $parentId = null){
        $pages = $this->getPages($languageId, $parentId);
        $tmpPages = array();
        foreach($pages as $page){
            $tmpPages = array_merge($tmpPages, $this->getAllPages($languageId, $page->getId()));
        }
        $answer = array_merge($pages, $tmpPages);
        return $answer;
    }

    public function getLink()
    {
        return ipContent()->getCurrentLanguage()->getLink() . $this->getUrl() . '/';
    }

    /**
     *
     * Get breadcrumb to current page.
     * @return Page[]
     *
     */

    public function getBreadcrumb($pageId = null){
        if ($pageId === null) {
            $currentPage = $this->getCurrentPage();
            if (!$currentPage) {
                return array();
            }
            $pageId = $currentPage->getId();
        }
        $pages = array();
        if ($pageId !== null) {
            $page = $this->getPage($pageId);
        } else {
            $page = $this->getCurrentPage();
        }

        if ($page) {
            $pages[] = $page;
            $parentPageId = $page->getParentId();
            while ($parentPageId !== null && $parentPageId !== false) {
                $parentPage = $this->getPage($parentPageId);
                $pages[] = $parentPage;
                $parentPageId = $parentPage->getParentId();
            }

        }
        return array_reverse($pages);
    }

    /**
     *
     * If you need to do some actions before any output, extend this class.
     * This function is always executed by the system when this zone is active (current page belongs to the zone).
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

    /** @return int Zone id */
    public function getId(){return $this->id;}
    /** @param $id int */
    public function setId($id){$this->id=$id;}

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
    public function setDescription($description){$this->description=$description;}

    /** @return string */
    public function getAssociatedModuleGroup(){return $this->associatedModuleGroup;}
    /** @param $associatedModuleGroup string */
    public function setAssociatedModuleGroup($associatedModuleGroup){$this->associatedModuleGroup=$associatedModuleGroup;}

    /** @return string */
    public function getAssociatedModule(){return $this->associatedModule;}
    /** @param $associatedModule string */
    public function setAssociatedModule($associatedModule){$this->associatedModule=$associatedModule;}

    /** @return string */
    public function getTitleInAdmin(){return $this->titleInAdmin;}

}


