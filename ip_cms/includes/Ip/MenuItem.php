<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Ip;


/**
 *
 * Menu item element class.
 *
 */
class MenuItem{

    /**
     * @var string
     */
    protected $title;

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


    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
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

    public function getSelected(){
        return $this->selected;
    }

    public function setSelected($selected){
        $this->selected = $selected;
    }

    public function getCurrent(){
        return $this->current;
    }

    public function setCurrent($current){
        $this->current = $current;
    }

    public function getChildren(){
        return $this->children;
    }

    public function setChildren($children){
        $this->children = $children;
    }

}