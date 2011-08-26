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
        
        
        if (!isset($_POST['widgetId']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['revisionId'])
            ) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        
        $widgetId = $_POST['widgetId'];
        $position = $_POST['position'];
        $blockName = $_POST['blockName'];
        $revisionId = $_POST['revisionId'];
        
        Model::moveInstance($instanceId, $revisionId, $blockName, $position);
                
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
            $widgetId = Model::createWidget($revisionId, $position, $blockName, $widgetName, $layouts[0]['name']);
            $widgetHtml = Model::generateWidgetPreview($widgetId, true);
            $widgetManagementHtml = Model::generateWidgetManagement($widgetId);
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
        
        
        
        $position = Model::getInstancePosition($instanceId);
  
        Model::deleteInstance($instanceId);
        
        $newWidgetId = Model::createWidget($widgetRecord['name'], $widgetRecord['data'], $widgetRecord['layout'], $widgetRecord['widgetId']);

        $newInstanceId = Model::addInstance($newWidgetId, $widgetRecord['revisionId'], $widgetRecord['blockName'], $position, $widgetRecord['visible']);
        
       
        $widgetObject = Model::getWidgetObject($widgetRecord['name']);
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
        
        if (!isset($_POST['widgetId'])) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        $widgetId = $_POST['widgetId'];
        
            
        if (!isset($_POST['revisionId'])) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }        
        $revisionId = $_POST['revisionId'];
        
        $widgetFullRecord = Model::getWidgetFullRecord($instanceId);
        
        if ($widgetFullRecord['predecessor'] !== null) {
            $widgetPosition = Model::getWidgetPosition($revisionId, $widgetFullRecord['blockName'], $widgetId);
            Model::addWidget($widgetFullRecord['predecessor'], $revisionId, $widgetFullRecord['blockName'], $position);
            Model::deleteWidget($widgetId, $revisionId);            
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
        