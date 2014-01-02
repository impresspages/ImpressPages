<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;


class AdminController extends \Ip\Controller
{

    public function index()
    {
        \Ip\Internal\Content\Service::setManagementMode(1);
        return (new \Ip\Response\Redirect(ipHomeUrl()));
    }

    public function setManagementMode()
    {
        Service::setManagementMode(intval(ipRequest()->getPost('value', 1)));
        return new \Ip\Response\Json(array(1));
    }

    private function getPagesList($language, $zone, $parentElementId = null)
    {
        $answer = '';
        $pages = $zone->getElements(
            $language['id'],
            $parentElementId,
            $startFrom = 0,
            $limit = null,
            $includeHidden = true,
            $reverseOrder = false
        );
        if ($pages && sizeof($pages) > 0) {
            $answer .= '<ul>' . "\n";
            foreach ($pages as $key => $page) {
                $answer .= '<li><a href="' . $page->getLink(true) . '">' . $page->getNavigationTitle() . '</a>';
                $answer .= $this->getPagesList($language, $zone, $page->getId());
                $answer .= '</li>';
            }
            $answer .= '</ul>' . "\n";
        }
        return $answer;
    }


    /**
     * @param $zone
     * @param $page
     * @return string content
     */
    private function _getPageDesignOptionsHtml($zone, $page, $data)
    {
        $data['defaultLayout'] = $zone->getLayout();
        $data['layouts'] = \Ip\Internal\Content\Model::getThemeLayouts();
        $data['layout'] = \Ip\Internal\Content\Service::getPageLayout($page);
        return \Ip\View::create('view/page_options_design.php', $data)->render();
    }


    public function moveWidget()
    {


        if (!isset($_POST['instanceId']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['revisionId'])
        ) {
            return $this->_errorAnswer('Missing POST variable');
        }

        $instanceId = $_POST['instanceId'];
        $position = (int)$_POST['position'];
        $blockName = $_POST['blockName'];
        $revisionId = $_POST['revisionId'];


        $record = Model::getWidgetFullRecord($instanceId);

        if (!$record) {
            return $this->_errorAnswer('Unknown instance ' . $instanceId);
        }

        Model::deleteInstance($instanceId);
        $newInstanceId = Model::addInstance(
            $record['widgetId'],
            $revisionId,
            $blockName,
            $position,
            $record['visible']
        );


        $widgetHtml = Model::generateWidgetPreview($newInstanceId, true);

        $data = array(
            'status' => 'success',
            'widgetHtml' => $widgetHtml,
            'oldInstance' => $instanceId,
            'newInstanceId' => $newInstanceId
        );

        return new \Ip\Response\Json($data);
    }

    public function createWidget()
    {


        if (!isset($_POST['widgetName']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['revisionId'])
        ) {
            return $this->_errorAnswer('Missing POST variable');
        }

        $widgetName = $_POST['widgetName'];
        $position = $_POST['position'];
        $blockName = $_POST['blockName'];
        $revisionId = $_POST['revisionId'];
        $columnId = empty($_POST['columnId']) ? null : $_POST['columnId'];

        if ($revisionId == '') {
            //Static block;
            $revisionId = null;
        } else {
            //check revision consistency
            $revisionRecord = \Ip\Revision::getRevision($revisionId);

            if (!$revisionRecord) {
                throw new Exception("Can't find required revision " . $revisionId, Exception::UNKNOWN_REVISION);
            }

            $zoneName = $revisionRecord['zoneName'];
            $pageId = $revisionRecord['pageId'];


            $zone = ipContent()->getZone($zoneName);
            if ($zone === false) {
                return $this->_errorAnswer('Unknown zone "' . $zoneName . '"');
            }

            $page = $zone->getPage($pageId);
            if ($page === false) {
                return $this->_errorAnswer('Page not found "' . $zoneName . '"/"' . $pageId . '"');
            }

        }

        $widgetObject = Model::getWidgetObject($widgetName);
        if ($widgetObject === false) {
            return $this->_errorAnswer('Unknown widget "' . $widgetName . '"');
        }


        try {
            $widgetId = Service::addWidget($widgetName);
            $instanceId = Service::addWidgetInstance($widgetId, $revisionId, $blockName, $position, true);
            $widgetHtml = Model::generateWidgetPreview($instanceId, 1);
        } catch (Exception $e) {
            return $this->_errorAnswer($e);
        }


        $data = array(
            'status' => 'success',
            'action' => '_createWidgetResponse',
            'widgetHtml' => $widgetHtml,
            'position' => $position,
            'widgetId' => $widgetId,
            'instanceId' => $instanceId
        );

        return new \Ip\Response\Json($data);

    }


    public function updateWidget()
    {

        $updateData = array();
        if (!isset($_POST['instanceId'])) {
            return $this->_errorAnswer('Missing POST variable instanceId');
        }
        $instanceId = $_POST['instanceId'];

        $record = Model::getWidgetFullRecord($instanceId);
        if (!$record) {
            return $this->_errorAnswer('Unknown widget instance id. ' . $instanceId);
        }


        if (!isset($_POST['widgetData']) || !is_array($_POST['widgetData'])) {
            $_POST['widgetData'] = array();
        }
        $postData = $_POST['widgetData'];

        $widgetObject = Model::getWidgetObject($record['name']);

        $newData = $widgetObject->update($record['widgetId'], $postData, $record['data']);
        $updateData['data'] = $newData;


        Model::updateWidget($record['widgetId'], $updateData);

        $data = array(
            'status' => 'success',
            'action' => '_updateWidget',
            'instanceId' => $instanceId
        );

        if (!empty($_POST['generatePreview'])) {
            $data['previewHtml'] = Model::generateWidgetPreview($instanceId, true);
        }


        return new \Ip\Response\Json($data);
    }

    public function changeLook()
    {
        $updateData = array();
        if (!isset($_POST['instanceId'])) {
            return $this->_errorAnswer('Missing POST variable instanceId');
        }
        $instanceId = $_POST['instanceId'];

        $record = Model::getWidgetFullRecord($instanceId);
        if (!$record) {
            return $this->_errorAnswer('Unknown widget instance id. ' . $instanceId);
        }


        if (!isset($_POST['look'])) {
            return $this->_errorAnswer('Missing POST variable look');
        }
        $look = $_POST['look'];
        $updateData['layout'] = $look;


        Model::updateWidget($record['widgetId'], $updateData);
        $previewHtml = Model::generateWidgetPreview($instanceId, true);

        $data = array(
            'status' => 'success',
            'action' => '_updateWidget',
            'previewHtml' => $previewHtml,
            'instanceId' => $instanceId
        );

        return new \Ip\Response\Json($data);
    }

    public function deleteWidget()
    {

        if (!isset($_POST['instanceId'])) {
            return $this->_errorAnswer('Missing instanceId POST variable');
        }
        $instanceId = (int)$_POST['instanceId'];

        Model::deleteInstance($instanceId);

        $data = array(
            'status' => 'success',
            'action' => '_deleteWidgetResponse',
            'widgetId' => $instanceId
        );

        return new \Ip\Response\Json($data);
    }


    public function savePage()
    {

        if (!isset($_POST['revisionId'])) {
            return $this->_errorAnswer('Missing revisionId POST variable');
        }
        $revisionId = $_POST['revisionId'];

        $publish = !empty($_POST['publish']);


        $revision = \Ip\Revision::getRevision($revisionId);

        if (!$revision) {
            return $this->_errorAnswer('Can\'t find revision. RevisionId \'' . $revisionId . '\'');
        }


        if ($publish) {
            $pageOptions = array();
            $pageOptions['lastModified'] = date("Y-m-d");
            \Ip\Internal\Pages\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);
            \Ip\Revision::publishRevision($revisionId);
        }


        $newRevisionId = \Ip\Revision::duplicateRevision($revisionId);

        $zone = ipContent()->getZone($revision['zoneName']);
        if (!$zone) {
            return $this->_errorAnswer('Can\'t find content management zone. RevisionId \'' . $revisionId . '\'');
        }

        $data = array(
            'status' => 'success',
            'action' => '_savePageResponse',
            'newRevisionId' => $newRevisionId,
            'newRevisionUrl' => $zone->getPage($revision['pageId'])->getLink() . '?cms_revision=' . $newRevisionId
        );

        return new \Ip\Response\Json($data);

    }




    private function _errorAnswer($errorMessage)
    {

        $data = array(
            'status' => 'error',
            'errorMessage' => $errorMessage
        );

        // TODO use jsonrpc response
        return new \Ip\Response\Json($data);
    }

}
