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

        Service::deleteWidgetInstance($instanceId);

        $newInstanceId = Service::addWidgetInstance(
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

    private function _errorAnswer($errorMessage)
    {

        $data = array(
            'status' => 'error',
            'errorMessage' => $errorMessage
        );

        // TODO use jsonrpc response
        return new \Ip\Response\Json($data);
    }

    public function createWidget()
    {
        ipRequest()->mustBePost();


        if (!isset($_POST['widgetName']) ||
            !isset($_POST['position']) ||
            !isset($_POST['block']) ||
            !isset($_POST['revisionId'])
        ) {
            return $this->_errorAnswer('Missing POST variable');
        }

        $widgetName = $_POST['widgetName'];
        $position = $_POST['position'];
        $blockName = $_POST['block'];
        $revisionId = $_POST['revisionId'];

        if ($revisionId == '') {
            //Static block;
            $revisionId = null;
        } else {
            //check revision consistency
            $revisionRecord = \Ip\Internal\Revision::getRevision($revisionId);

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
            $widgetId = Service::createWidget($widgetName);
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
            'block' => $blockName,
            'instanceId' => $instanceId
        );

        return new \Ip\Response\Json($data);

    }


    public function addWidgetToSide()
    {
        ipRequest()->mustBePost();

        if (!isset($_POST['widgetName']) ||
            !isset($_POST['targetWidgetInstanceId']) ||
            !isset($_POST['leftOrRight'])
        ) {
            return $this->_errorAnswer('Missing POST variable');
        }

        $widgetName = $_POST['widgetName'];
        $targetWidgetInstanceId = $_POST['targetWidgetInstanceId'];
        $leftOrRight = $_POST['leftOrRight'];

        $instance = InstanceModel::getInstance($targetWidgetInstanceId);
        if (!$instance) {
            throw new \Ip\Exception("Instance doesn't exist.");
        }


        //create column widget
        $columnWidgetId = Service::createWidget($widgetName, $widgetData);

        //add column widget instance
        $position = Service::getInstancePosition($targetWidgetInstanceId) - 1;
        Service::addWidgetInstance($columnWidgetId, $instance['revisionId'], $instance['blockName'], $position, 1);



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
            $data['generateHtml'] = Model::generateWidgetPreview($instanceId, true);
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
            'generateHtml' => $previewHtml,
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

        Service::deleteWidgetInstance($instanceId);

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


        $revision = \Ip\Internal\Revision::getRevision($revisionId);

        if (!$revision) {
            return $this->_errorAnswer('Can\'t find revision. RevisionId \'' . $revisionId . '\'');
        }


        if ($publish) {
            $pageOptions = array();
            $pageOptions['lastModified'] = date("Y-m-d");
            \Ip\Internal\Pages\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);
            \Ip\Internal\Revision::publishRevision($revisionId);
        }


        $newRevisionId = \Ip\Internal\Revision::duplicateRevision($revisionId);

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

}
