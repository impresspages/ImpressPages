<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


require_once(__DIR__.'/model.php');

class Controller{
    

    public function initVariables(){
        global $site;
        
        header('Content-type: text/javascript');
        $revision = $site->getRevision();
        $data = array (
            'ipBaseUrl' => BASE_URL,
        	'ipZoneName' => $site->getCurrentZone()->getName(),
        	'ipPageId' => $site->getCurrentElement()->getId(),
        	'ipRevisionId' => $revision['id']
        );
        $answer = \Ip\View::create('standard/content_management/view/init_variables.php', $data)->render();
        $site->setOutput($answer);
    }
    

    public function initManagementData(){
        global $site;
        
        $widgets = Model::getAvailableWidgetObjects();
        
        $data = array (
            'widgets' => $widgets
        );
        
        $controlPanelHtml = \Ip\View::create('standard/content_management/view/control_panel.php', $data)->render();
        
        $widgetControllsHtml = \Ip\View::create('standard/content_management/view/widget_controlls.php', $data)->render();
        
        $data = array (
            'status' => 'success',
            'controlPanelHtml' => $controlPanelHtml,
        	'widgetControllsHtml' => $widgetControllsHtml
        );
        
        $this->_outputAnswer($data);
    }
        
    
    public function createWidget(){
        global $site;
        
        
        $error = false;
        
        if (!isset($_POST['widgetName']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['revisionId'])
            ) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        
        $widgetName = $_POST['widgetName'];
        $position = $_POST['position'];
        $blockName = $_POST['blockName'];
        $revisionId = $_POST['revisionId'];
        
        
        $revisionRecord = Model::getRevision($revisionId);
        
        if ($revisionRecord === false) {
        	throw new Exception("Can't find required revision " . $revisionId); 
        }
        
        $zoneName = $revisionRecord['zoneName'];
        $pageId = $revisionRecord['pageId'];
        
        
        $widgetObject = Model::getWidgetObject($widgetName);
        if ($widgetObject === false) {
            $this->_errorAnswer('Unknown widget "'.$widgetName.'"');
            return;
        }
        
        $zone = $site->getZone($zoneName);
        if ($zone === false) {
            $this->_errorAnswer('Unknown zone "'.$zoneName.'"');
            return;
        }
        
        $page = $zone->getElement($pageId);
        if ($page === false) {
            $this->_errorAnswer('Page not found "'.$zoneName.'"/"'.$pageId.'"');
            return;
        }
        
        
        
        try {
            $layouts = $widgetObject->getLayouts();
            $widgetId = Model::createWidget($revisionId, $position, $blockName, $widgetName, $layouts[0]['name']);
            $widgetHtml = Model::generateWidgetManagement($widgetId);
        } catch (Exception $e) {
            $this->_errorAnswer($e);
            return;
        }


        $data = array (
            'status' => 'success',
            'action' => '_createWidgetResponse',
            'widgetHtml' => $widgetHtml,
            'position' => $position,
        	'widgetId' => $widgetId
        );
        
        $this->_outputAnswer($data);
      
    }
    
    public function manageWidget() {
        global $site;
        
        if (!isset($_POST['widgetId'])) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        
        $widgetId = $_POST['widgetId'];
        
        $managementHtml = Model::generateWidgetManagement($widgetId);
        
        $data = array (
            'status' => 'success',
            'action' => '_manageWidgetResponse',
            'managementHtml' => $managementHtml,
            'widgetId' => $widgetId
        );
        
        $this->_outputAnswer($data);        
    }
    
    public function previewWidget() {
        global $site;
        
        if (!isset($_POST['widgetId'])) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        
        $widgetId = $_POST['widgetId'];
        
        $previewHtml = Model::generateWidgetPreview($widgetId, true);
        
        $data = array (
            'status' => 'success',
            'action' => '_manageWidgetResponse',
            'previewHtml' => $previewHtml,
            'widgetId' => $widgetId
        );
        
        $this->_outputAnswer($data);        
    }    
    

    public function updateWidget(){
        if (!isset($_POST['widgetId'])) {
            $this->_errorAnswer('Mising POST variable websiteId');
            return;
        }
        if (!isset($_POST['widgetData']) && is_array($_POST['widgetData'])) {
            $this->_errorAnswer('Mising POST variable: widgetData');
            return;
        }
        
        $widgetId = $_POST['widgetId'];
        
        Model::setWidgetData($widgetId, $_POST['widgetData']);
        
        $data = array (
            'status' => 'success',
            'action' => '_updateWidget',
            'widgetId' => $widgetId
        );
        
        $this->_outputAnswer($data);              
    }
    
    public function deleteWidget() {
        global $site;
        
        if (!isset($_POST['widgetId'])) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        
        $widgetId = $_POST['widgetId'];
        
        $managementHtml = Model::deleteWidget($widgetId);
        
        $data = array (
            'status' => 'success',
            'action' => '_deleteWidgetResponse',
            'managementHtml' => $managementHtml,
            'widgetId' => $widgetId
        );
        
        $this->_outputAnswer($data);   
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
        