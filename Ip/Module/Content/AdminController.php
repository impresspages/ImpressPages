<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Module\Content;


class AdminController extends \Ip\Controller
{

    public function index()
    {
        header('location: ' . \Ip\Config::baseUrl('', array('cms_action' => 'manage')));
    }

    public function getSitemapInList()
    {
        $answer = '';
        $answer .= '<ul id="ipSitemap">' . "\n";

        $answer .= '<li><a href="' . \Ip\Config::baseUrl('') . '">Home</a></li>' . "\n";

        $languages = \Ip\Frontend\Db::getLanguages(true); //get all languages including hidden

        foreach ($languages as $language) {
            $link = \Ip\Internal\Deprecated\Url::generate($language['id']);
            $answer .= '<li><a href="' . $link . '">' . htmlspecialchars($language['d_long']) . ' (' . htmlspecialchars(
                    $language['d_short']
                ) . ')</a>' . "\n";

            $zones = ipGetZones();
            if (sizeof($zones) > 0) {
                $answer .= '<ul>';
                foreach ($zones as $key => $zone) {
                    $answer .= '<li><a href="' . \Ip\Internal\Deprecated\Url::generate(
                            $language['id'],
                            $zone->getName()
                        ) . '">' . $zone->getTitle() . '</a>' . "\n";
                    $answer .= $this->getPagesList($language, $zone);
                    $answer .= '</li>' . "\n";
                }
                $answer .= '</ul>';

            }

            $answer .= '</li>' . "\n";
        }


        $answer .= '<ul>' . "\n";

        $answer = str_replace('?cms_action=manage', '', $answer);
        $answer = str_replace('&cms_action=manage', '', $answer);

        return new \Ip\Response($answer);
    }

    private function getPagesList($language, $zone, $parentElementId = null) {
        $answer = '';
        $pages = $zone->getElements($language['id'], $parentElementId, $startFrom = 0, $limit = null, $includeHidden = true, $reverseOrder = false);
        if($pages && sizeof($pages) > 0) {
            $answer .= '<ul>'."\n";
            foreach($pages as $key => $page) {
                $answer .= '<li><a href="'.$page->getLink(true).'">'.$page->getButtonTitle().'</a>';
                $answer .= $this->getPagesList($language, $zone, $page->getId());
                $answer .= '</li>';
            }
            $answer .= '</ul>'."\n";
        }
        return $answer;
    }


    public function getPageOptionsHtml() {
        if (!isset($_REQUEST['pageId'])) {
            return $this->_errorAnswer('Page id is not set');
        }

        $pageId = (int)$_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            return $this->_errorAnswer('Zone name is not set');
        }

        $zone = ipGetZone($_REQUEST['zoneName']);

        if (!($zone)) {
            return $this->_errorAnswer('Can\'t find zone');
        }

        $element = $zone->getElement($pageId);

        if (! $element) {
            return $this->_errorAnswer('Page does not exist');
        }

        $data = array(
            'element' => $element
        );

        $tabs = array();
        $title = __('General', 'ipAdmin');
        $content = \Ip\View::create('view/page_options_general.php', $data)->render();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = __('SEO', 'ipAdmin');
        $content = \Ip\View::create('view/page_options_seo.php', $data)->render();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = __('Advanced', 'ipAdmin');
        $content = \Ip\View::create('view/page_options_advanced.php', $data)->render();
        $tabs[] = array('title' => $title, 'content' => $content);

        $title = __('Design', 'ipAdmin');
        $content = $this->_getPageDesignOptionsHtml($zone, $element, array('show_confirm_notification' => true));
        $tabs[] = array('title' => $title, 'content' => $content);

        $optionsHtml = \Ip\View::create('view/page_options.php', array('tabs' => $tabs))->render();
        $answer = array(
            'status' => 'success',
            'optionsHtml' => $optionsHtml
        );

        return new \Ip\Response\Json($answer);
    }

    /**
     * @param $zone
     * @param $page
     * @return string content
     */
    private function _getPageDesignOptionsHtml($zone, $page, $data)
    {
        $data['defaultLayout'] = $zone->getLayout();
        $data['layouts'] = \Ip\Module\Content\Model::getThemeLayouts();

        $data['layout'] = \Ip\Frontend\Db::getPageLayout(
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

        $tmpWidgets = Model::getAvailableWidgetObjects();
        $tmpWidgets = Model::sortWidgets($tmpWidgets);
        $widgets = array();
        foreach($tmpWidgets as $key => $widget) {
            if (!$widget->getUnderTheHood()) {
                $widgets[$key] = $widget;
            }
        }

        $revisions = \Ip\Revision::getPageRevisions(ipGetCurrentZone()->getName(), ipGetCurrentPage()->getId());

        $managementUrls = array();
        foreach($revisions as $revision) {
            $managementUrls[] = ipGetCurrentPage()->getLink().'&cms_revision='.$revision['revisionId'];
        }

        $revision = \Ip\ServiceLocator::getContent()->getRevision();

        $manageableRevision = isset($revisions[0]['revisionId']) && ($revisions[0]['revisionId'] == $revision['revisionId']);

        $page = ipGetCurrentPage();

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

        return new \Ip\Response\Json($data);
    }

    public function moveWidget() {


        if (!isset($_POST['instanceId']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['revisionId']) ||
            !isset($_POST['managementState'])
        ) {
            return $this->_errorAnswer('Mising POST variable');
        }

        $instanceId = $_POST['instanceId'];
        $position = (int)$_POST['position'];
        $blockName = $_POST['blockName'];
        $revisionId = $_POST['revisionId'];
        $managementState = $_POST['managementState'];


        $record = Model::getWidgetFullRecord($instanceId);

        if (!$record)
        {
            return $this->_errorAnswer('Unknown instance '.$instanaceId);
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

        return new \Ip\Response\Json($data);
    }

    public function createWidget() {



        if (!isset($_POST['widgetName']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['revisionId'])
        ) {
            return $this->_errorAnswer('Mising POST variable');
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


            $zone = ipGetZone($zoneName);
            if ($zone === false) {
                return $this->_errorAnswer('Unknown zone "'.$zoneName.'"');
            }

            $page = $zone->getElement($pageId);
            if ($page === false) {
                return $this->_errorAnswer('Page not found "'.$zoneName.'"/"'.$pageId.'"');
            }

        }
        $widgetObject = Model::getWidgetObject($widgetName);
        if ($widgetObject === false) {
            return $this->_errorAnswer('Unknown widget "'.$widgetName.'"');
        }


        try {
            $layouts = $widgetObject->getLayouts();
            $widgetId = Model::createWidget($widgetName, array(), $layouts[0]['name'], null);
            $instanceId = Model::addInstance($widgetId, $revisionId, $blockName, $position, true);
            $widgetManagementHtml = Model::generateWidgetManagement($instanceId);
        } catch (Exception $e) {
            return $this->_errorAnswer($e);
        }


        $data = array (
            'status' => 'success',
            'action' => '_createWidgetResponse',
            'widgetManagementHtml' => $widgetManagementHtml,
            'position' => $position,
            'widgetId' => $widgetId,
            'instanceId' => $instanceId
        );

        return new \Ip\Response\Json($data);

    }

    public function manageWidget() {

        if (!isset($_POST['instanceId'])) {
            return $this->_errorAnswer('Mising POST variable');
        }
        $instanceId = $_POST['instanceId'];




        $widgetRecord = Model::getWidgetFullRecord($instanceId);

        if ($widgetRecord === false){
            throw new Exception('Can\'t find widget '.$instanceId, Exception::UNKNOWN_INSTANCE);
        }




        $widgetObject = Model::getWidgetObject($widgetRecord['name']);

        if (!$widgetObject) {
            return $this->_errorAnswer("Controlls of this widget does not exist. You need to install required plugin \"" . $widgetRecord['name'] . "\" or remove this widget");
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

        return new \Ip\Response\Json($data);
    }




    public function cancelWidget() {

        if (!isset($_POST['instanceId'])) {
            return $this->_errorAnswer('Mising POST variable');
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

            return new \Ip\Response\Json($data);
        } else {
            Model::deleteInstance($instanceId);
            $data = array (
                'status' => 'success',
                'action' => '_cancelWidgetResponse',
                'previewHtml' => '',
                'instanceId' => $instanceId,
                'oldInstanceId' => ''
            );

            return new \Ip\Response\Json($data);
        }


    }


    public function updateWidget(){
        if (!isset($_POST['instanceId'])) {
            return $this->_errorAnswer('Mising POST variable instanceId');
        }
        $instanceId = $_POST['instanceId'];

        if (!isset($_POST['layout'])) {
            return $this->_errorAnswer('Mising POST variable layout');
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

        return new \Ip\Response\Json($data);
    }

    public function deleteWidget() {

        if (!isset($_POST['instanceId'])) {
            return $this->_errorAnswer('Mising instanceId POST variable');
        }
        $instanceId = (int)$_POST['instanceId'];

        Model::deleteInstance($instanceId);

        $data = array (
            'status' => 'success',
            'action' => '_deleteWidgetResponse',
            'widgetId' => $instanceId
        );

        return new \Ip\Response\Json($data);
    }


    public function savePage () {

        if (!isset($_POST['revisionId'])) {
            return $this->_errorAnswer('Mising revisionId POST variable');
        }
        $revisionId = $_POST['revisionId'];

        if (isset($_POST['pageOptions'])){
            $pageOptions = $_POST['pageOptions'];
        }

        $revision = \Ip\Revision::getRevision($revisionId);

        if (!$revision) {
            return $this->_errorAnswer('Can\'t find revision. RevisionId \''.$revisionId.'\'');
        }

        $newRevisionId = \Ip\Revision::duplicateRevision($revisionId);

        $zone = ipGetZone($revision['zoneName']);
        if (!$zone) {
            return $this->_errorAnswer('Can\'t find content management zone. RevisionId \''.$revisionId.'\'');
        }

        $data = array (
            'status' => 'success',
            'action' => '_savePageResponse',
            'newRevisionId' => $newRevisionId,
            'newRevisionUrl' => $zone->getElement($revision['pageId'])->getLink().'&cms_revision='.$newRevisionId
        );

        return new \Ip\Response\Json($data);

    }


    public function savePageOptions () {
        if (empty($_POST['revisionId'])) {
            return $this->_errorAnswer('Mising revisionId POST variable');
        }
        $revisionId = $_POST['revisionId'];


        if (empty($_POST['pageOptions'])){
            return $this->_errorAnswer('Mising pageOptions POST variable');
        }
        $pageOptions = $_POST['pageOptions'];

        $revision = \Ip\Revision::getRevision($revisionId);

        if (!$revision) {
            return $this->_errorAnswer('Can\'t find revision. RevisionId \''.$revisionId.'\'');
        }

        $page = \Ip\Module\Pages\Db::getPage($revision['pageId']);
        if (isset($pageOptions['url']) && $pageOptions['url'] != $page['url']) {
            $changedUrl = true;
        } else {
            $changedUrl = false;
        }

        if ($changedUrl) {
            $zone = ipGetZone($revision['zoneName']);
            $oldElement = $zone->getElement($revision['pageId']);
            $oldUrl = $oldElement->getLink();
        }

        \Ip\Module\Pages\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);

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


        new \Ip\Response\Json($data);

    }

    public function publishPage () {

        if (!isset($_POST['revisionId'])) {
            return $this->_errorAnswer('Mising revisionId POST variable');
        }
        $revisionId = $_POST['revisionId'];
        $revision = \Ip\Revision::getRevision($revisionId);



        $pageOptions = array();
        $pageOptions['lastModified'] = date("Y-m-d");
        \Ip\Module\Pages\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);


        \Ip\Revision::publishRevision($revisionId);


        $lastRevision = \Ip\Revision::getLastRevision($revision['zoneName'], $revision['pageId']);
        if ($lastRevision['revisionId'] == $revision['revisionId']) {
            $newRevisionUrl = ipGetCurrentPage()->getLink(); //we publish the last revision. We will not specify revision id. Then CMS will create new revison for editing.
        } else {
            $newRevisionUrl = ipGetCurrentPage()->getLink().'&cms_revision='.$lastRevision['revisionId'];
        }

        $data = array (
            'status' => 'success',
            'action' => '_publishPageResponse',
            'newRevisionUrl' => $newRevisionUrl
        );

        return new \Ip\Response\Json($data);
    }


    private function _errorAnswer($errorMessage) {

        $data = array (
            'status' => 'error',
            'errorMessage' => $errorMessage
        );

        // TODOX use jsonrpc response
        return new \Ip\Response\Json($data);
    }

}
