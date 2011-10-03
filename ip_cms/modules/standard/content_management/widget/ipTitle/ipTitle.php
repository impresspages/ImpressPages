<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management\widget;
if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');

class ipTitle extends \Modules\standard\content_management\Widget{


    public function managementHtml($instanceId, $data, $layout){
        if (!isset($data['title'])) {
           $data['title'] = '';    
        }

        $answer = \Ip\View::create('view/management.php', $data)->render();
        
        return $answer;
        
    }
    
    
    public function previewHtml($instanceId, $data, $layout){
        if (!isset($data['title'])) {
           $data['title'] = '';    
        }
        
        $answer = \Ip\View::create('view/preview.php', $data)->render();        
        
        return $answer;
    }    
    
    public function getTitle(){
        return 'Title';
    }
    
    public function getIcon(){
        return MODULE_DIR.'standard/content_management/widget/ipTitle/icon.gif';
    }
    
    public function getName(){
        return 'IpTitle';
    }    
    
  
    
}