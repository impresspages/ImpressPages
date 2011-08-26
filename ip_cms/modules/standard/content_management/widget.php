<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;

abstract class Widget{


    public static function post ($data) {
        
    }
    
    public static function getLayouts() {
        $answer = array();
        $answer[] = array('name' => 'default');
        return $answer;
    }
    
    public static function duplicate($oldId, $newId) {
            
    }
    
    public static function delete($widgetId){

    }
    
    public static function managementHtml($widgetId, $data) {
        
    }
    
    public static function previewHtml($widgetId, $data) {
        
    }
    
    public static function getTitle() {
        
    }
    
    public static function getIcon() {
        
    }
    
    public static function getName() {
        
    }
    

}