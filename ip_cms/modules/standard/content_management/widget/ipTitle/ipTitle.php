<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');

class Widget_ipTitle extends Widget{


    public static function managementHtml($instanceId, $data){
        if (!isset($data['title'])) {
           $data['title'] = '';    
        }

        $answer = \Ip\View::create('view/management.php', $data)->render();
        
        return $answer;
        
    }
    
    
    public static function previewHtml($instanceId, $data){
        if (!isset($data['title'])) {
           $data['title'] = '';    
        }
        
        $answer = \Ip\View::create('view/preview.php', $data)->render();        
        
        return $answer;
    }    
    
    public static function getTitle(){
        return 'Title';
    }
    
    public static function getIcon(){
        return MODULE_DIR.'standard/content_management/widget/ipTitle/icon.gif';
    }
    
    public static function getName(){
        return 'ipTitle';
    }    
    
  
    
}