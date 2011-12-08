<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


require_once(__DIR__.'/model.php');
require_once(__DIR__.'/exception.php');

class Controller{



    public function initManagementData(){
        global $site;

        $widgets = Model::getAvailableWidgetObjects();
        $revisions = \Ip\Db::getPageRevisions($site->getCurrentZone()->getName(), $site->getCurrentElement()->getId());

        $managementUrls = array();
        foreach($revisions as $revisionKey => $revision) {
            $managementUrls[] = $site->getCurrentElement()->getLink().'&cms_revision='.$revision['revisionId'];
        }

        $revision = $site->getRevision();

        $manageableRevision = $revisions[0]['revisionId'] == $revision['revisionId'];

        $data = array (
            'widgets' => $widgets,
            'revisions' => $revisions,
            'currentRevision' => $revision,
            'managementUrls' => $managementUrls,
            'manageableRevision' => $manageableRevision
        );

        $controlPanelHtml = \Ip\View::create('view/control_panel.php', $data)->render();

        $widgetControlsHtml = \Ip\View::create('view/widget_controls.php', $data)->render();

        $saveProgressHtml = \Ip\View::create('view/save_progress.php', $data)->render();
        $data = array (
            'status' => 'success',
            'controlPanelHtml' => $controlPanelHtml,
            'widgetControlsHtml' => $widgetControlsHtml,
            'saveProgressHtml' => $saveProgressHtml,
            'manageableRevision' => $manageableRevision
        );

        $this->_outputAnswer($data);
    }

    public function widgetPost() {
        global $site;


        if (!isset($_POST['instanceId'])) {
            $this->_errorAnswer('Mising widgetId POST variable');
            return;
        }
        $instanceId = $_POST['instanceId'];

        $widgetRecord = Model::getWidgetFullRecord($instanceId);

        try {
            if ($widgetRecord) {
                $widgetObject = Model::getWidgetObject($widgetRecord['name']);
                if ($widgetObject) {
                    $answer = $widgetObject->post($instanceId, $_POST, $widgetRecord['data']);
                    $this->_outputAnswer($answer);
                } else {
                    throw new Exception("Can't find requested Widget: ".$widgetRecord['name'], Exception::UNKNOWN_WIDGET);
                }
            } else {
                throw new Exception("Can't find requested Widget: ".$instanceId, Exception::UNKNOWN_INSTANCE);
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
            throw new Exception("Can't find required revision " . $revisionId, Exception::UNKNOWN_REVISION);
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
            $widgetManagementHtml = Model::generateWidgetManagement($instanceId);
        } catch (Exception $e) {
            $this->_errorAnswer($e);
            return;
        }


        $data = array (
            'status' => 'success',
            'action' => '_createWidgetResponse',
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
            throw new Exception('Can\'t find widget '.$instanceId, Exception::UNKNOWN_INSTANCE);
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

    //    public function previewWidget() {
    //        global $site;
    //
    //        if (!isset($_POST['instanceId'])) {
    //            $this->_errorAnswer('Mising POST variable');
    //            return;
    //        }
    //        $instanceId = $_POST['instanceId'];
    //
    //        $previewHtml = Model::generateWidgetPreview($instanceId, true);
    //
    //        $data = array (
    //            'status' => 'success',
    //            'action' => '_manageWidgetResponse',
    //            'previewHtml' => $previewHtml,
    //            'widgetId' => $widgetId
    //        );
    //
    //        $this->_outputAnswer($data);
    //    }


    public function cancelWidget() {
        global $site;

        if (!isset($_POST['instanceId'])) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }
        $instanceId = $_POST['instanceId'];

        $curPosition = Model::getInstancePosition($instanceId);
        $widgetFullRecord = Model::getWidgetFullRecord($instanceId);

        if ($widgetFullRecord['predecessor'] !== null) {
            $widgetPositin = Model::getInstancePosition($instanceId);
            Model::deleteInstance($instanceId);
            $newInstanceId = Model::addInstance($widgetFullRecord['predecessor'], $widgetFullRecord['revisionId'], $widgetFullRecord['blockName'], $curPosition, $widgetFullRecord['visible']);

            $previewHtml = Model::generateWidgetPreview($newInstanceId, true);

            $data = array (
                'status' => 'success',
                'action' => '_cancelWidgetResponse',
                'previewHtml' => $previewHtml,
                'instanceId' => $newInstanceId
            );

            $this->_outputAnswer($data);
        } else {
            $data = array (
                'status' => 'error',
                'action' => '_cancelWidgetResponse',
                'errorMessage' => 'Widget has no predecessor.',
            );

            $this->_outputAnswer($data);
        }


    }


    public function updateWidget(){
        if (!isset($_POST['instanceId'])) {
            $this->_errorAnswer('Mising POST variable instanceId');
            return;
        }
        $instanceId = $_POST['instanceId'];

        if (!isset($_POST['layout'])) {
            $this->_errorAnswer('Mising POST variable layout');
            return;
        }
        $layout = $_POST['layout'];


        if (!isset($_POST['widgetData']) && is_array($_POST['widgetData'])) {
            $this->_errorAnswer('Mising POST variable: widgetData');
            return;
        }

        $postData = $_POST['widgetData'];


        $record = Model::getWidgetFullRecord($instanceId);

        $widgetObject = Model::getWidgetObject($record['name']);

        $newData = $widgetObject->prepareData($instanceId, $postData, $record['data']);

        $updateArray = array (
            'data' => $newData,
            'layout' => $layout
        );

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
            'action' => '_savePageResponse',
            'newRevisionId' => $newRevisionId,
            'newRevisionUrl' => $site->getCurrentElement()->getLink().'&cms_revision='.$newRevisionId 
        );

        $this->_outputAnswer($data);

    }

    public function publishPage () {
        global $site;

        if (!isset($_POST['revisionId'])) {
            $this->_errorAnswer('Mising revisionId POST variable');
            return;
        }
        $revisionId = $_POST['revisionId'];

        \Ip\Db::publishRevision($revisionId);

        
        $revision = \Ip\Db::getRevision($revisionId);
        
        $lastRevision = \Ip\Db::getLastRevision($revision['zoneName'], $revision['pageId']);
        if ($lastRevision['revisionId'] == $revision['revisionId']) {
            $newRevisionUrl = $site->getCurrentElement()->getLink(); //we publish the last revision. We will not specify revision id. Then CMS will create new revison for editing.
        } else {
            $newRevisionUrl = $site->getCurrentElement()->getLink().'&cms_revision='.$revisionId; 
        }

        $data = array (
            'status' => 'success',
            'action' => '_publishPageResponse',
            'newRevisionUrl' => $newRevisionUrl 
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
