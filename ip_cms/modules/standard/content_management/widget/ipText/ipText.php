<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');

class ipText extends \Modules\standard\content_management\Widget{
    
    public function managementHtml($widgetId, $data, $layout){
    	
    	if (!isset($data['text'])) {
    	   $data['text'] = '';    
    	}
    	$answer = \Ip\View::create('view/management.php', $data)->render();
    	
        return $answer;
    }
    
    
    public function previewHtml($instanceId, $data, $layout){
        
        $answer = '';
        
        if(isset($data['text'])) {
            $answer = $data['text'];
        }
        return $answer;
    }
    
    public function getTitle(){
        return 'Text';
    }
    
    public function getIcon(){
        return MODULE_DIR.'standard/content_management/widget/ipText/icon.gif';
    }
    
    public function getName(){
        return 'IpText';
    }    
    

    
}