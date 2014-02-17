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
            'newInstanceId' => $newInstanceId,
            'block' => $blockName
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

            $pageId = $revisionRecord['pageId'];

            $page = new \Ip\Page($pageId);
            if ($page === false) {
                return $this->_errorAnswer('Page not found #' . $pageId);
            }

        }

        $widgetObject = Model::getWidgetObject($widgetName, true);
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
            $data['html'] = Model::generateWidgetPreview($instanceId, true);
        }


        return new \Ip\Response\Json($data);
    }

    public function changeSkin()
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


        if (!isset($_POST['skin'])) {
            return $this->_errorAnswer('Missing POST variable skin');
        }
        $skin = $_POST['skin'];
        $updateData['layout'] = $skin;


        Model::updateWidget($record['widgetId'], $updateData);
        $previewHtml = Model::generateWidgetPreview($instanceId, true);

        $data = array(
            'status' => 'success',
            'action' => '_updateWidget',
            'html' => $previewHtml,
            'instanceId' => $instanceId
        );

        return new \Ip\Response\Json($data);
    }

    public function deleteWidget()
    {

        if (!isset($_POST['instanceId'])) {
            return $this->_errorAnswer('Missing instanceId POST variable');
        }
        $instanceId = $_POST['instanceId'];

        if (is_array($instanceId)) {
            foreach($instanceId as $curInstanceId) {
                Service::deleteWidgetInstance($curInstanceId);
            }
        } else {
            $instanceId = (int)$instanceId;
            Service::deleteWidgetInstance($instanceId);
        }



        $data = array(
            'status' => 'success'
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
            \Ip\Internal\Pages\Model::updatePageProperties($revision['pageId'], $pageOptions);
            \Ip\Internal\Revision::publishRevision($revisionId);
        }


        $newRevisionId = \Ip\Internal\Revision::duplicateRevision($revisionId);

        $page = new \Ip\Page($revision['pageId']);

        $data = array(
            'status' => 'success',
            'action' => '_savePageResponse',
            'newRevisionId' => $newRevisionId,
            'newRevisionUrl' => $page->getLink() . '?cms_revision=' . $newRevisionId
        );

        return new \Ip\Response\Json($data);

    }

	private function _addPageToTree(\Ip\Page $page) {
		$p = array(
            'text' => $page->getPageTitle(),
            'icon' => 'fa fa-file-text',
            'li_attr' => (object) array(
            	'data-url' => $page->getUrl()
			),
			'children' => array()
		);
        foreach ($page->getChildren() as $child) {
        	$p['children'][] = $this->_addPageToTree($child);
        }
        return $p;
	}

	public function getPageTree() {
        $sitemap = array(
			array(
			   'text' => 'EN',
			   'type' => 'language',
			   'icon' => 'fa fa-flag-o',
			   'state' => array('opened' => true),
			   'children' => array()
			)
		);

		// @todo: get all the languages

//        foreach($zones as $zone) {
//            $z = array(
//                'text' => $zone->getTitle(),
//                'icon' => 'fa fa-folder-o',
//            	'li_attr' => (object) array(
//            		'data-url' => parse_url($zone->getUrl(),PHP_URL_PATH)
//				),
//            	'children'=>array()
//			);
//			foreach (\Ip\Menu\Helper::getMenuItems($zone->getName()) as $page) {
//			   $z['children'][] = $this->_addPageToTree($page);
//			}
//
//			// @todo: add zone to correct language
//			$sitemap[0]['children'][] = $z;
//        }

        $data = array(
            'status' => 'success',
            'sitemap' => $sitemap
        );

        return new \Ip\Response\Json($data);
	}

}
