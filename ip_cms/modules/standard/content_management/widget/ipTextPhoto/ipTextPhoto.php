<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');

class Widget_ipTextPhoto extends Widget{


    
    public static function post($data) {
                    
    }
    
    public static function managementHtml($widgetId, $data){
    	
    	if (!isset($data['text'])) {
    	   $data['text'] = '';    
    	}
    	

    	
    	$answer = \Ip\View::create('view/management.php', $data)->render();
    	
        return $answer;
    }
    
    
    public static function previewHtml($widgetId, $data){
        
        $answer = '';
        
        if(isset($data['text'])) {
            $answer = $data['text'];
        }
        return $answer;
    }
    
    public static function getTitle(){
        return 'Text with photo';
    }
    
    public static function getIcon(){
        return MODULE_DIR.'standard/content_management/widget/ipTextPhoto/icon.gif';
    }
    
    public static function getName(){
        return 'ipTextPhoto';
    }    
    

    
}