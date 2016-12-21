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


        if (!isset($_POST['widgetId']) ||
            !isset($_POST['position']) ||
            !isset($_POST['blockName']) ||
            !isset($_POST['revisionId']) ||
            !isset($_POST['languageId'])
        ) {
            return $this->_errorAnswer('Missing POST variable');
        }

        $widgetId = $_POST['widgetId'];
        $position = (int)$_POST['position'];
        $blockName = $_POST['blockName'];
        $revisionId = isset($_POST['revisionId']) ? $_POST['revisionId'] : 0;
        $languageId = isset($_POST['languageId']) ? $_POST['languageId'] : 0;

        Service::moveWidget($widgetId, $position, $blockName, $revisionId, $languageId);

        $widgetHtml = Model::generateWidgetPreview($widgetId, true);

        $data = array(
            'status' => 'success',
            'widgetHtml' => $widgetHtml,
            'newwidgetId' => $widgetId,
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


    public function getImageContainerHtml()
    {
        $html = ipView('view/imageContainer.php', [])->render();

        $result = array(
            "status" => "success",
            "html" => $html
        );

        // TODO JsonRpc
        return new \Ip\Response\Json($result);
    }


    public function createWidget()
    {
        ipRequest()->mustBePost();


        if (!isset($_POST['widgetName']) ||
            !isset($_POST['position']) ||
            !isset($_POST['block']) ||
            !isset($_POST['revisionId']) ||
            !isset($_POST['languageId'])
        ) {
            return $this->_errorAnswer('Missing POST variable');
        }

        $widgetName = $_POST['widgetName'];
        $position = $_POST['position'];
        $blockName = $_POST['block'];
        $revisionId = isset($_POST['revisionId']) ? $_POST['revisionId'] : 0;
        $languageId = isset($_POST['languageId']) ? $_POST['languageId'] : 0;

        if ($revisionId) {
            //check revision consistency
            $revisionRecord = \Ip\Internal\Revision::getRevision($revisionId);

            if (!$revisionRecord) {
                throw new \Ip\Exception\Content("Can't find required revision " . esc($revisionId));
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
            $widgetId = Service::createWidget(
                $widgetName,
                null,
                null,
                $revisionId,
                $languageId,
                $blockName,
                $position,
                true
            );
            if ($widgetName == 'Columns' && $revisionId == 0) {
                $widgetRecord = Model::getWidgetRecord($widgetId);
                $data = array_merge($widgetRecord['data'], array('static' => true));
                Model::updateWidget($widgetId, array('data' => $data));

            }
            $widgetHtml = Model::generateWidgetPreview($widgetId, 1);
        } catch (\Ip\Exception\Content $e) {
            return $this->_errorAnswer($e);
        }


        $data = array(
            'status' => 'success',
            'action' => '_createWidgetResponse',
            'widgetHtml' => $widgetHtml,
            'position' => $position,
            'widgetId' => $widgetId,
            'block' => $blockName
        );

        return new \Ip\Response\Json($data);

    }


    public function updateWidget()
    {

        $updateData = [];
        if (!isset($_POST['widgetId'])) {
            return $this->_errorAnswer('Missing POST variable widgetId');
        }
        $widgetId = $_POST['widgetId'];

        $record = Model::getWidgetRecord($widgetId);
        if (!$record) {
            return $this->_errorAnswer('Unknown widget. ' . $widgetId);
        }


        if (!isset($_POST['widgetData']) || !is_array($_POST['widgetData'])) {
            $_POST['widgetData'] = [];
        }
        $postData = $_POST['widgetData'];

        $widgetObject = Model::getWidgetObject($record['name']);

        $newData = $widgetObject->update($record['id'], $postData, $record['data']);
        $updateData['data'] = $newData;


        Model::updateWidget($record['id'], $updateData);

        $data = array(
            'status' => 'success',
            'action' => '_updateWidget',
            'widgetId' => $widgetId
        );

        if (!empty($_POST['generatePreview'])) {
            $data['html'] = Model::generateWidgetPreview($widgetId, true);
        }


        return new \Ip\Response\Json($data);
    }

    public function changeSkin()
    {
        $updateData = [];
        if (!isset($_POST['widgetId'])) {
            return $this->_errorAnswer('Missing POST variable widgetId');
        }
        $widgetId = $_POST['widgetId'];

        $record = Model::getWidgetRecord($widgetId);
        if (!$record) {
            return $this->_errorAnswer('Unknown widget. ' . $widgetId);
        }


        if (!isset($_POST['skin'])) {
            return $this->_errorAnswer('Missing POST variable skin');
        }
        $skin = $_POST['skin'];
        $skin = basename($skin); //to avoid any path manipulation
        $updateData['skin'] = $skin;


        Model::updateWidget($record['id'], $updateData);
        $previewHtml = Model::generateWidgetPreview($widgetId, true);

        $data = array(
            'status' => 'success',
            'action' => '_updateWidget',
            'html' => $previewHtml,
            'widgetId' => $widgetId
        );

        return new \Ip\Response\Json($data);
    }

    public function deleteWidget()
    {

        if (!isset($_POST['widgetId'])) {
            return $this->_errorAnswer('Missing widgetId POST variable');
        }
        $widgetId = $_POST['widgetId'];

        if (is_array($widgetId)) {
            foreach ($widgetId as $curId) {
                Service::deleteWidget($curId);
            }
        } else {
            $widgetId = (int)$widgetId;
            Service::deleteWidget($widgetId);
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
            $pageOptions = [];
            $pageOptions['updatedAt'] = date("Y-m-d");
            $pageOptions['isVisible'] = 1;
            \Ip\Internal\Pages\Model::updatePageProperties($revision['pageId'], $pageOptions);
        }
        \Ip\Internal\Revision::publishRevision($revisionId);


        $newRevisionId = \Ip\Internal\Revision::duplicateRevision($revisionId);

        $page = new \Ip\Page($revision['pageId']);

        $data = array(
            'status' => 'success',
            'action' => '_savePageResponse',
            'newRevisionId' => $newRevisionId,
            'newRevisionUrl' => $page->getLink()
        );

        return new \Ip\Response\Json($data);

    }

    private function _addPageToTree(\Ip\Page $page)
    {
        $children = array(
            'text' => $page->getTitle(),
            'icon' => 'fa fa-file-text',
            'li_attr' => (object)array(
                    'data-url' => $page->getLink()
                ),
            'children' => []
        );
        foreach ($page->getChildren() as $child) {
            $children['children'][] = $this->_addPageToTree($child);
        }
        return $children;
    }

    public function getPageTree()
    {

        $language = ipContent()->getCurrentLanguage();

        $sitemap = array(
            array(
                'text' => $language->getAbbreviation(),
                'type' => 'language',
                'icon' => 'fa fa-flag-o',
                'state' => array('opened' => true),
                'children' => []
            )
        );

        $menuList = \Ip\Internal\Pages\Model::getMenuList($language->getCode());

        foreach ($menuList as $menu) {
            $page = ipPage($menu['id']);

            $children = array(
                'text' => $menu['title'],
                'icon' => 'fa fa-folder-o',
                'li_attr' => (object)array(
                        'data-url' => $page->getLink(),
                    ),
                'children' => []
            );

            foreach ($page->getChildren() as $child) {
                $children['children'][] = $this->_addPageToTree($child);
            }

            $sitemap[0]['children'][] = $children;
        }

        $data = array(
            'status' => 'success',
            'sitemap' => $sitemap
        );

        return new \Ip\Response\Json($data);
    }

}
