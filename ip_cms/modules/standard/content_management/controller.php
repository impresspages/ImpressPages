<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


require_once(__DIR__.'/model_widget.php');

class Controller{
    

    public function initVariables(){
        global $site;
        
        header('Content-type: text/javascript');
        $data = array (
            'ipBaseUrl' => BASE_URL
        );
        $answer = \Ip\View::create('standard/content_management/view/init_variables.php', $data)->render();
        $site->setOutput($answer);
    }
    

    public function initManagementData(){
        global $site;
        
        $widgets = ModelWidget::getWidgets();
        
        $data = array (
            'widgets' => $widgets
        );
        
        $controlPanelHtml = \Ip\View::create('standard/content_management/view/control_panel.php', $data)->render();
        
        $data = array (
            'status' => 'success',
            'controlPanelHtml' => $controlPanelHtml,
        );
        
        $this->_outputAnswer($data);
    }
        
    
    public function createWidget(){
        global $site;
        
        
        $error = false;
        
        if (!isset($_POST['widgetName']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['zoneName']) ||
            !isset($_POST['pageId']) 
            ) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        
        $widgetName = $_POST['widgetName'];
        $position = $_POST['position'];
        $blockName = $_POST['blockName'];
        $zoneName = $_POST['zoneName'];
        $pageId = $_POST['pageId'];
        
        
        $widget = ModelWidget::getWidget($widgetName);
        if ($widget === false) {
            $this->_errorAnswer('Unknown widget "'.$widgetName.'"');
            return;
        }
        
        $zone = $site->getZone($zoneName);
        if ($zone === false) {
            $this->_errorAnswer('Unknown zone "'.$zoneName.'"');
            return;
        }
        
        $page = $zone->getPage($pageId);
        if ($page === false) {
            $this->_errorAnswer('Page not found "'.$zoneName.'"/"'.$pageId.'"');
            return;
        }
        
        
        
        try {
            $layouts = $widget->getLayouts();
            $widgetId = ModelWidget::createWidget($widgetName, $position, $zoneName, $pageId, $blockName, $layouts[0]['name']);
            $widgetHtml = ModelWidget::generateWidgetManagement($widgetId);
            //$widget->generateHtml($widgetId, $data, $layout) 
        } catch (Exception $e) {
            $this->_errorAnswer($e);
            return;
        }

        
        $html = 
        
        header('Content-type: text/json; charset=utf-8');
        
        $data = array (
            'status' => 'success',
            'widgetHtml' => '<div class="ipWidget" style="border: 1px solid; background-color: #565;">TEST<br /><br /> -- MANO WIDGETAS --<br /></div>'
        );
        
        $answer = json_encode($data);        
        $site->setOutput($answer);            
    }
    
    public function updateWidget() {
        
        
    }
    
    public function deleteWidget($id, $data) {
        
        
    }
    
    private function _errorAnswer($errorMessage) {
        $data = array (
            'status' => 'error',
            'action' => '_createWidgetResponse',
            'errorMessage' => $errorMessage
        );
        
        $this->_outputAnswer($data);
    }
    
    private function _outputAnswer($data) {
        global $site;
        header('Content-type: text/json; charset=utf-8');
        $answer = json_encode($data);        
        $site->setOutput($answer);         
    }
    
}        
        