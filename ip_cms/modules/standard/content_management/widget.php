<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;

abstract class Widget{

    public static function getLayouts() {
        $answer = array();
        $answer[] = array('name' => 'default', 'title' => 'Default');
        return $answer;
    }
    
    abstract public function getTitle();
    
    abstract public function getIcon();
    
    abstract public function getName();
    

}