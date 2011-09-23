<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');

class Widget_ipPicture extends Widget{


    
    public static function post($instanceId, $postData, $data) {
        $answer = '';
        
        $newData = array();
        
        
        
        Model::updateInstance($instanceId, $newData);
        
        
        return $answer;  
    }
    
    public static function managementHtml($instanceId, $data){
    	
    	if (!isset($data['picture'])) {
    	   $data['picture'] = '';    
    	}
    	
        if (!isset($data['pictureBig'])) {
           $data['pictureBig'] = '';    
        }
        
        if (!isset($data['title'])) {
           $data['title'] = '';    
        }
        
    	$data['instanceId'] = $instanceId;
    	
    	$answer = \Ip\View::create('view/management.php', $data)->render();
    	
        return $answer;
    }
    
    
    public static function previewHtml($widgetId, $data){
        
        $answer = '';

        
        $answer = \Ip\View::create('view/preview.php', $data)->render();        
        
        return $answer;
    }
    
    public static function getTitle(){
        return 'Picture';
    }
    
    public static function getIcon(){
        return MODULE_DIR.'standard/content_management/widget/'.self::getName().'/icon.gif';
    }
    
    public static function getName(){
        return 'ipPicture';
    }    
    

    
}