<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Menu;


/**
 *
 * Menu item element class.
 *
 */
class Item
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $pageTitle;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string link target attribute. Eg. _blank
     */
    protected $target;

    /**
     * @var bool true if this menu item is within breadcrumb
     */
    protected $selected;

    /**
     * @var bool true if this menu item represents currently opened page
     */
    protected $current;

    /**
     * @var MenuItem[]
     */
    protected $children;

    /**
     * @var int
     */
    protected $depth;

    /**
     * @var string
     */
    protected $type;


    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function getPageTitle(){
        return $this->pageTitle;
    }

    public function setPageTitle($pageTitle){
        $this->pageTitle = $pageTitle;
    }

    public function getUrl(){
        return $this->url;
    }

    public function setUrl($url){
        $this->url = $url;
    }

    public function getTarget(){
        return $this->target;
    }

    public function setTarget($target){
        $this->target = $target;
    }

    public function isInCurrentBreadcrumb(){
        return (bool) $this->selected;
    }

    public function markAsInCurrentBreadcrumb($selected){
        $this->selected = (bool) $selected;
    }

    public function isCurrent(){
        return (bool) $this->current;
    }

    public function markAsCurrent($current){
        $this->current = (bool) $current;
    }

    public function getChildren(){
        if (!is_array($this->children)) {
            return array();
        }
        return $this->children;
    }

    public function setChildren($children){
        if (!is_array($children)) {
            throw new \Exception("SetChildren expects array of \\Ip\\Menu\\Item");
        }
        $this->children = $children;
    }

    public function getDepth(){
        return (int) $this->depth;
    }

    public function setDepth($depth){
        $this->depth = $depth;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }

    public function getBlank(){
        return (bool) $this->blank;
    }

    public function setBlank($blank){
        $this->blank = $blank;
    }
}
