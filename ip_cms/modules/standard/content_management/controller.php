<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


require_once(__DIR__.'/model.php');
require_once(__DIR__.'/exception.php');
require_once(BASE_DIR.MODULE_DIR.'standard/menu_management/db.php');

class Controller extends \Ip\Controller{


    
    public function allowAction($action) {
        switch($action) {
            case 'widgetPost':
                return true;
                
                break;
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

        $title = $parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'design');
        $content = $this->_getPageDesignOptionsHtml($zone, $element, array('show_confirm_notification' => true));
        $tabs[] = array('title' => $title, 'content' => $content);

        $optionsHtml = \Ip\View::create('view/page_options.php', array('tabs' => $tabs))->render();
        $answer = array(
            'status' => 'success',
            'optionsHtml' => $optionsHtml
        );
        self::_outputAnswer($answer);
    }

    /**
     * @param $zone
     * @param $page
     * @return string content
     */
    private function _getPageDesignOptionsHtml($zone, $page, $data)
    {
        $data['defaultLayout'] = $zone->getLayout();
        $data['layouts'] = \Modules\standard\content_management\Model::getThemeLayouts();

        $data['layout'] = \Frontend\Db::getPageLayout(
            $zone->getAssociatedModuleGroup(),
            $zone->getAssociatedModule(),
            $page->getId()
        );

        if (!$data['layout']) {
            $data['layout'] = $data['defaultLayout'];
        }

        return \Ip\View::create('view/page_options_design.php', $data)->render();
    }
    

    public function initManagementData(){
        global $site;

        $tmpWidgets = Model::getAvailableWidgetObjects();
        $tmpWidgets = \Modules\developer\widgets\Model::sortWidgets($tmpWidgets);
        $widgets = array();
        foreach($tmpWidgets as $key => $widget) {
            if (!$widget->getUnderTheHood()) {
                $widgets[$key] = $widget;
            }
        }

        $revisions = \Ip\Revision::getPageRevisions($site->getCurrentZone()->getName(), $site->getCurrentElement()->getId());

        $managementUrls = array();
        foreach($revisions as $revision) {
            $managementUrls[] = $site->getCurrentElement()->getLink().'&cms_revision='.$revision['revisionId'];
        }

        $revision = $site->getRevision();

        $manageableRevision = isset($revisions[0]['revisionId']) && ($revisions[0]['revisionId'] == $revision['revisionId']);
        
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
            $this->_errorAnswer('Mising instanceId POST variable');
            return;
        }
        $instanceId = $_POST['instanceId'];

        $widgetRecord = Model::getWidgetFullRecord($instanceId);

        try {
            if ($widgetRecord) {
                $widgetObject = Model::getWidgetObject($widgetRecord['name']);
                if ($widgetObject) {
                    $widgetObject->post($this, $instanceId, $_POST, $widgetRecord['data']);
                } else {
                    throw new Exception("Can't find requested Widget: ".$widgetRecord['name'], Exception::UNKNOWN_WIDGET);
                }
            } else {
                throw new Exception("Can't find requested Widget: ".$instanceId, Exception::UNKNOWN_INSTANCE);
            }
        } catch (Exception $e) {
            $this->_errorAnswer($e->getMessage());
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

        if ($revisionId == '') {
            //Static block;
            $revisionId = null;
        } else {
            //check revision consistency
            $revisionRecord = \Ip\Revision::getRevision($revisionId);

            if ($revisionRecord === false) {
                throw new Exception("Can't find required revision " . $revisionId, Exception::UNKNOWN_REVISION);
            }

            $zoneName = $revisionRecord['zoneName'];
            $pageId = $revisionRecord['pageId'];


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

        }
        $widgetObject = Model::getWidgetObject($widgetName);
        if ($widgetObject === false) {
            $this->_errorAnswer('Unknown widget "'.$widgetName.'"');
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
            'widgetId' => $widgetId,
            'instanceId' => $instanceId
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
            'instanceId' => $instanceId,
            'newInstanceId' => $newInstanceId
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
            Model::deleteInstance($instanceId);
            $newInstanceId = Model::addInstance($widgetFullRecord['predecessor'], $widgetFullRecord['revisionId'], $widgetFullRecord['blockName'], $curPosition, $widgetFullRecord['visible']);
            $previewHtml = Model::generateWidgetPreview($newInstanceId, true);
            $data = array (
                'status' => 'success',
                'action' => '_cancelWidgetResponse',
                'previewHtml' => $previewHtml,
                'oldInstanceId' => $newInstanceId
            );

            $this->_outputAnswer($data);
        } else {
            Model::deleteInstance($instanceId);
            $data = array (
                'status' => 'success',
                'action' => '_cancelWidgetResponse',
                'previewHtml' => '',
                'instanceId' => $instanceId,
                'oldInstanceId' => ''
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

        $data = array (
            'status' => 'success',
            'action' => '_savePageResponse',
            'newRevisionId' => $newRevisionId,
            'newRevisionUrl' => $zone->getElement($revision['pageId'])->getLink().'&cms_revision='.$newRevisionId 
        );

        $this->_outputAnswer($data);

    }
    
    
    public function savePageOptions () {
        global $site;
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
            $this->_errorAnswer('Can\'t find revision. RevisionId \''.$revisionId.'\'');
            return;
        }

        $page = \Modules\standard\menu_management\Db::getPage($revision['pageId']);
        if (isset($pageOptions['url']) && $pageOptions['url'] != $page['url']) {
            $changedUrl = true;
        } else {
            $changedUrl = false;
        }

        if ($changedUrl) {
            $zone = $site->getZone($revision['zoneName']);
            $oldElement = $zone->getElement($revision['pageId']);
            $oldUrl = $oldElement->getLink();
        }

        \Modules\standard\menu_management\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);

        if ($changedUrl) {
            $newElement = $zone->getElement($revision['pageId']);
            $newUrl = $newElement->getLink();
        }

        $data = array (
            'status' => 'success',
            'action' => '_savePageOptionsResponse',
            'pageOptions' => $pageOptions,
        );

        if ($changedUrl) {
            $data['oldUrl'] = $oldUrl;
            $data['newUrl'] = $newUrl;
        }


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
        if (isset($data['managementHtml'])) {
           // $data['managementHtml'] = utf8_encode($data['managementHtml']);
        }
        $answer = json_encode(\Library\Php\Text\Utf8::checkEncoding($data));
        $site->setOutput($answer);
    }

}
