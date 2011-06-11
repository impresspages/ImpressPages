<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;

class Widget{
    var $title;
    var $name;
    var $icon;

    public function __construct($name, $title) {
        $this->setName($name);
        $this->setTitle($title);
        $this->setIcon(MODULE_DIR.'standard/content_management/img/widget.gif');
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getTitle() {
        return $this->title;        
    }
    
    public function setIcon($icon) {
        $this->icon = $icon;
    }
    
    public function getIcon() {
        return $this->icon;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName () {
        return $this->name;
    }
    

}