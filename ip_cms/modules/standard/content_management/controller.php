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
            'ipManagementUrl' => $site->generateUrl(),
        	'ipZoneName' => $site->getCurrentZone()->getName(),
        	'ipPageId' => $site->getCurrentElement()->getId(),
        	'ipRevisionId' => $revision['revisionId']
        );
        $answer = \Ip\View::create('view/init_variables.php', $data)->render();
        $site->setOutput($answer);
    }
    

    public function initManagementData(){
        global $site;
        
        $widgets = Model::getAvailableWidgetObjects();
        $revisions = \Ip\Db::getPageRevisions($site->getCurrentZone()->getName(), $site->getCurrentElement()->getId());
        
        $managementUrls = array();
        foreach($revisions as $revisionKey => $revision) {
           $managementUrls[] = $site->getCurrentElement()->getLink().'&cms_revision='.$revision['revisionId']; 
        }
        
        $revision = $site->getRevision();
        
        $data = array (
            'widgets' => $widgets,
            'revisions' => $revisions,
            'currentRevisionId' => $revision['revisionId'],
            'managementUrls' => $managementUrls 
        );
        
        $controlPanelHtml = \Ip\View::create('view/control_panel.php', $data)->render();
        
        $widgetControls1Html = \Ip\View::create('view/widget_controls1.php', $data)->render();
        $widgetControls2Html = \Ip\View::create('view/widget_controls2.php', $data)->render();
        
        $saveProgressHtml = \Ip\View::create('view/save_progress.php', $data)->render();
        
        $data = array (
            'status' => 'success',
            'controlPanelHtml' => $controlPanelHtml,
        	'widgetControls1Html' => $widgetControls1Html,
            'widgetControls2Html' => $widgetControls2Html,
            'saveProgressHtml' => $saveProgressHtml
        );
        
        $this->_outputAnswer($data);
    }
        
    public function widgetPost() {
        global $site;
        
        
        if (!isset($_POST['widgetId'])) {
            $this->_errorAnswer('Mising widgetId POST variable');
            return;
        }
        $widgetId = $_POST['widgetId'];
        
        $widgetRecord = Model::getWidgetRecord($widgetId);

        try {
            if ($widgetRecord) {
                $widgetObject = Model::getWidgetObject($widgetRecord['name']);
                if ($widgetObject) {
                    $answer = $widgetObject->post($_POST);
                    $this->_outputAnswer($answer);
                } else {
                    throw new \Exception("Can't find requested Widget: ".$widgetRecord['name']);
                }
            } else {
                throw new \Exception("Can't find requested Widget: ".$widgetId);
            }
        } catch (Exception $e) {
            $this->_errorAnswer($e);            
        }
        
    }
    
    public function moveWidget() {
        global $site;
        
        
        if (!isset($_POST['instanceId']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['revisionId'])
            ) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        
        $instanceId = $_POST['instanceId'];
        $position = $_POST['position'];
        $blockName = $_POST['blockName'];
        $revisionId = $_POST['revisionId'];
        
        
        $record = Model::getWidgetFullRecord($instanceId);
        Model::deleteInstance($instanceId);
        Model::addInstance($record['widgetId'], $revisionId, $blockName, $position, $record['visible']);
                
        $data = array (
            'status' => 'success'
        );
        
        $this->_outputAnswer($data);        
    }
    
    public function createWidget() {
        global $site;
        
        
        
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
        
        
        $revisionRecord = \Ip\Db::getRevision($revisionId);
        
        if ($revisionRecord === false) {
        	throw new \Exception("Can't find required revision " . $revisionId); 
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
            $widgetId = Model::createWidget($widgetName, array(), $layouts[0]['name'], null);
            $instanceId = Model::addInstance($widgetId, $revisionId, $blockName, $position, true);
            $widgetHtml = Model::generateWidgetPreview($instanceId, true);
            $widgetManagementHtml = Model::generateWidgetManagement($instanceId);
        } catch (Exception $e) {
            $this->_errorAnswer($e);
            return;
        }


        $data = array (
            'status' => 'success',
            'action' => '_createWidgetResponse',
            'widgetHtml' => $widgetHtml,
            'widgetManagementHtml' => $widgetManagementHtml,
            'position' => $position,
        	'widgetId' => $widgetId
        );
        
        $this->_outputAnswer($data);
      
    }
    
    public function manageWidget() {
        global $site;
        
        if (!isset($_POST['instanceId'])) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }        
        $instanceId = $_POST['instanceId'];
        
        
        
        
        $widgetRecord = Model::getWidgetFullRecord($instanceId);

        if ($widgetRecord === false){
            throw new \Exception('Can\'t find widget '.$instanceId);
        }
        
        
        

        $widgetObject = Model::getWidgetObject($widgetRecord['name']);
        
        if (!$widgetObject) {        
            $this->_errorAnswer("Controlls of this widget does not exist. You need to install required plugin \"" . $widgetRecord['name'] . "\" or remove this widget");
            return;
        }
        
        
        $position = Model::getInstancePosition($instanceId);
        Model::deleteInstance($instanceId);
        
        $newWidgetId = Model::createWidget($widgetRecord['name'], $widgetRecord['data'], $widgetRecord['layout'], $widgetRecord['widgetId']);
        $newInstanceId = Model::addInstance($newWidgetId, $widgetRecord['revisionId'], $widgetRecord['blockName'], $position, $widgetRecord['visible']);
        
        $widgetObject->duplicate($widgetRecord['widgetId'], $newWidgetId);
        
        
               
        
        $managementHtml = Model::generateWidgetManagement($newInstanceId);
        
        $data = array (
            'status' => 'success',
            'action' => '_manageWidgetResponse',
            'managementHtml' => $managementHtml,
            'instanceId' => $instanceId
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
    
    
    public function cancelWidget() {
        global $site;
        
        if (!isset($_POST['instanceId'])) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        $instanceId = $_POST['instanceId'];
        
            
        $widgetFullRecord = Model::getWidgetFullRecord($instanceId);
        
        if ($widgetFullRecord['predecessor'] !== null) {
            $widgetPositin = Model::getInstancePosition($instanceId);
            Model::deleteInstance($instanceId);
            Model::addInstance($widgetFullRecord['predecessor'], $revisionId, $widgetFullRecord['blockName'], $position, $widgetFullRecord['visible']);
                        
        }
        
        
        $previewHtml = Model::generateWidgetPreview($widgetFullRecord['predecessor'], true);
        
        $data = array (
            'status' => 'success',
            'action' => '_cancelWidgetResponse',
            'previewHtml' => $previewHtml,
            'widgetId' => $widgetId
        );
        
        $this->_outputAnswer($data);        
    }       
    

    public function updateWidget(){
        if (!isset($_POST['instanceId'])) {
            $this->_errorAnswer('Mising POST variable instanceId');
            return;
        }
        $instanceId = $_POST['instanceId'];
        
        if (!isset($_POST['widgetData']) && is_array($_POST['widgetData'])) {
            $this->_errorAnswer('Mising POST variable: widgetData');
            return;
        }
        $widgetData = $_POST['widgetData'];
        
        
        $updateArray = array (
            'data' => $widgetData
        );
        
        $record = Model::getWidgetFullRecord($instanceId);
        
        Model::updateWidget($record['widgetId'], $updateArray);
        $previewHtml = Model::generateWidgetPreview($instanceId, true);
        
        $data = array (
            'status' => 'success',
            'action' => '_updateWidget',
            'previewHtml' => $previewHtml,
            'instanceId' => $instanceId
        );
        
        $this->_outputAnswer($data);              
    }
    
    public function deleteWidget() {
        global $site;
        
        if (!isset($_POST['instanceId'])) {
            $this->_errorAnswer('Mising instanceId POST variable');
            return;
        }
        $instanceId = $_POST['instanceId'];
        
        Model::deleteInstance($instanceId);
        
        $data = array (
            'status' => 'success',
            'action' => '_deleteWidgetResponse',
            'widgetId' => $instanceId
        );
        
        $this->_outputAnswer($data);   
    }
    
    
    public function savePage () {
        global $site;
            
        if (!isset($_POST['revisionId'])) {
            $this->_errorAnswer('Mising revisionId POST variable');
            return;
        }        
        $revisionId = $_POST['revisionId'];
        
        $newRevisionId = \Ip\Db::duplicateRevision($revisionId);
        
        $data = array (
            'status' => 'success',
            'action' => '_deleteWidgetResponse',
            'newRevisionId' => $newRevisionId,
            'newRevisionUrl' => $site->getCurrentElement()->getLink().'&cms_revision='.$newRevisionId 
        );
        
        $this->_outputAnswer($data);   
        
    }
    
    private function _errorAnswer($errorMessage) {
        $data = array (
            'status' => 'error',
            'errorMessage' => $errorMessage
        );
        
        $this->_outputAnswer($data);
    }
    
    private function _outputAnswer($data) {
        global $site;
        //header('Content-type: text/json; charset=utf-8'); throws save file dialog on firefox if iframe is used
        $answer = json_encode($data);        
        $site->setOutput($answer);         
    }
    
   
    
}        
        