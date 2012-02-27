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
require_once(BASE_DIR.MODULE_DIR.'standard/menu_management/db.php');

class Controller extends \Ip\Controller{


    
    public function allowAction($action) {
        switch($action) {
            case 'getPageOptionsHtml':
                if (\Ip\Backend::loggedIn()) {
                    return \Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'content_management') || \Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'menu_management');
                } else {
                    return false;
                }
                
                break;
            default:
                if (\Ip\Backend::loggedIn()) {
                    return \Ip\Backend::userHasPermission(\Ip\Backend::userId(), 'standard', 'content_management');
                } else {
                    return false;
                }
        }
    }
    
    public function getPageOptionsHtml() {
        global $site;
        global $parametersMod;
        if (!isset($_REQUEST['pageId'])) {
            $this->_errorAnswer('Page id is not set');
            return;
        }

        $pageId = $_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            $this->_errorAnswer('Zone name is not set');
            return;
        }

        $zone = $site->getZone($_REQUEST['zoneName']);

        if (!($zone)) {
            $this->_errorAnswer('Can\'t find zone');
            return;
        }

        $element = $zone->getElement($pageId);

        if (! $element) {
            $this->_errorAnswer('Page does not exist');
            return;
        }
        
        $data = array(
            'element' => $element
        );
        
        $tabs = array();
        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'general');
        $content = \Ip\View::create('view/page_options_general.php', $data)->render();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'seo');
        $content = \Ip\View::create('view/page_options_seo.php', $data)->render();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'advanced');
        $content = \Ip\View::create('view/page_options_advanced.php', $data)->render();
        $tabs[] = array('title' => $title, 'content' => $content);
        
        
        $optionsHtml = \Ip\View::create('view/page_options.php', array('tabs' => $tabs))->render();
        $answer = array(
            'status' => 'success',
            'optionsHtml' => $optionsHtml
        );
        self::_outputAnswer($answer);
    }
    

    public function initManagementData(){
        global $site;

        $widgets = Model::getAvailableWidgetObjects();
        $widgets = \Modules\developer\widgets\Model::sortWidgets($widgets);
        $revisions = \Ip\Revision::getPageRevisions($site->getCurrentZone()->getName(), $site->getCurrentElement()->getId());

        $managementUrls = array();
        foreach($revisions as $revisionKey => $revision) {
            $managementUrls[] = $site->getCurrentElement()->getLink().'&cms_revision='.$revision['revisionId'];
        }

        $revision = $site->getRevision();

        $manageableRevision = $revisions[0]['revisionId'] == $revision['revisionId'];
        
        $page = $site->getCurrentElement();

        $data = array (
            'widgets' => $widgets,
            'page' => $page,
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
        !isset($_POST['revisionId']) ||
        !isset($_POST['managementState'])
        ) {
            $this->_errorAnswer('Mising POST variable');
            return;
        }

        $instanceId = $_POST['instanceId'];
        $position = (int)$_POST['position'];
        $blockName = $_POST['blockName'];
        $revisionId = $_POST['revisionId'];
        $managementState = $_POST['managementState'];
        

        $record = Model::getWidgetFullRecord($instanceId);
        
        if (!$record)
        {
            $this->_errorAnswer('Unknown instance '.$instanaceId);
            return;
        }
        
        Model::deleteInstance($instanceId);
        $newInstanceId = Model::addInstance($record['widgetId'], $revisionId, $blockName, $position, $record['visible']);

        
        //preview and management might depend on instanceId. We need to regenerate moved widget.
        if ($managementState) {
            $widgetHtml = Model::generateWidgetManagement($newInstanceId);
        } else {
            $widgetHtml = Model::generateWidgetPreview($newInstanceId, true);
        }
        
        $data = array (
            'status' => 'success',
            'widgetHtml' => $widgetHtml,
            'oldInstance' => $instanceId,
            'newInstanceId' => $newInstanceId
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


        $revisionRecord = \Ip\Revision::getRevision($revisionId);

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

        $widgetObject->duplicate($widgetRecord['widgetId'], $newWidgetId, $widgetRecord['data']);


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
            if ($widgetFullRecord['predecessor']) {
                $newInstanceId = Model::addInstance($widgetFullRecord['predecessor'], $widgetFullRecord['revisionId'], $widgetFullRecord['blockName'], $curPosition, $widgetFullRecord['visible']);
                $previewHtml = Model::generateWidgetPreview($newInstanceId, true);
            } else {
                $newInstanceId = '';
                $previewHtml = '';
            }

            $data = array (
                'status' => 'success',
                'action' => '_cancelWidgetResponse',
                'previewHtml' => $previewHtml,
                'oldInstanceId' => $newInstanceId
            );

            $this->_outputAnswer($data);
        } else {
            Model::deleteInstance($instanceId);
            Model::deleteUnusedWidgets();
            $data = array (
                'status' => 'success',
                'action' => '_cancelWidgetResponse',
                'previewHtml' => '',
                'oldInstanceId' =>null
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


        if (!isset($_POST['widgetData']) || !is_array($_POST['widgetData'])) {
            $_POST['widgetData'] = array();
        }

        $postData = $_POST['widgetData'];


        $record = Model::getWidgetFullRecord($instanceId);

        $widgetObject = Model::getWidgetObject($record['name']);

        $newData = $widgetObject->update($record['widgetId'], $postData, $record['data']);

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
        
        if (isset($_POST['pageOptions'])){
            $pageOptions = $_POST['pageOptions'];
        }

        $revision = \Ip\Revision::getRevision($revisionId);
        
        if (!$revision) {
            $this->_errorAnswer('Can\'t find revision. RevisionId \''.$revisionId.'\'');
            return;
        }
        
        $newRevisionId = \Ip\Revision::duplicateRevision($revisionId);
        
        $zone = $site->getZone($revision['zoneName']);
        if (!$zone) {
            $this->_errorAnswer('Can\'t find content management zone. RevisionId \''.$revisionId.'\'');
            return;
        }
        
        \Modules\standard\menu_management\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);
        
        $data = array (
            'status' => 'success',
            'action' => '_savePageResponse',
            'newRevisionId' => $newRevisionId,
            'newRevisionUrl' => $zone->getElement($revision['pageId'])->getLink().'&cms_revision='.$newRevisionId 
        );

        $this->_outputAnswer($data);

    }
    
    
    public function savePageOptions () {
        if (empty($_POST['revisionId'])) {
            $this->_errorAnswer('Mising revisionId POST variable');
            return;
        }
        $revisionId = $_POST['revisionId'];
        
        
        if (empty($_POST['pageOptions'])){
            $this->_errorAnswer('Mising pageOptions POST variable');
            return;
        }
        $pageOptions = $_POST['pageOptions'];
        
        $revision = \Ip\Revision::getRevision($revisionId);
        
        if (!$revision) {
            $this->_errorAnswer('Can\'t find revision. RvisionId \''.$revisionId.'\'');
            return;
        }
        
        \Modules\standard\menu_management\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);
        
        $data = array (
            'status' => 'success',
            'action' => '_savePageOptionsResponse'
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
        $revision = \Ip\Revision::getRevision($revisionId);
        
        
        
        $pageOptions = array();
        $pageOptions['lastModified'] = date("Y-m-d");
        \Modules\standard\menu_management\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);
        
        
        \Ip\Revision::publishRevision($revisionId);

        
        $lastRevision = \Ip\Revision::getLastRevision($revision['zoneName'], $revision['pageId']);
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
