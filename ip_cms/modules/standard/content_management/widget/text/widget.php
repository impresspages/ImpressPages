<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');

class WidgetText extends Widget{

    public function __construct() {
        
    }
    
    public function managementHtml($widgetId, $data){
    	$data = array (
    	);
    	$answer = \Ip\View::create('standard/content_management/widget/text/view/management.php', $data)->render();
    	
        return $answer;
    }
    
    
    public function previewHtml($widgetId, $data){
        
        $answer = 'Text widget:<br/>';
        if(isset($data['text'])) {
            $answer .= $data['text'];
        }
        return $answer;
    }
    
    public function getTitle(){
        return 'Text';
    }
    
    public function getIcon(){
        return MODULE_DIR.'standard/content_management/widget/text/icon.gif';
    }
    
    public function getName(){
        return 'text';
    }    
    

    
}