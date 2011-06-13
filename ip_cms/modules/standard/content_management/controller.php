<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


class Controller{
    

    public function initVariables(){
        global $site;
        
//        header("content-type: application/x-javascript");
        header('Content-type: text/javascript');
        //header('Content-type: text/css');
        $data = array (
            'ipBaseUrl' => BASE_URL
        );
        $answer = \Ip\View::create('standard/content_management/view/init_variables.php', $data)->render();
        $site->setOutput($answer);
    }
    

    public function initManagementData(){
        global $site;
        global $dispatcher;
        
        header('Content-type: text/json; charset=utf-8');
        
        $event = new \Ip\Event($this, 'contentManagement.collectWidgets', null);
        $event->setValue('widgets', array());
        $dispatcher->notify($event);
        
        $widgets = $event->getValue('widgets');
        
        $data = array (
            'widgets' => $widgets
        );
        
        $controlPanelHtml = \Ip\View::create('standard/content_management/view/control_panel.php', $data)->render();
        
        $data = array (
            'status' => 'success',
            'controlPanelHtml' => $controlPanelHtml,
        );
        
        $answer = json_encode($data);        
        $site->setOutput($answer);
    }
        
    
    public function createWidget(){
        global $site;
        $data = array (
            'status' => 'success',
            'widgetHtml' => '<li class="ipWidgetSelector"><div style="border: 1px solid; background-color: #565;">TEST<br /><br /> -- MANO WIDGETAS --<br /><div></li>'
        );
        
        $answer = json_encode($data);        
        $site->setOutput($answer);            
    }
    
    public function updateWidget() {
        
    }
    
    public function deleteWidget($id, $data) {
        
    }
    
}        
        