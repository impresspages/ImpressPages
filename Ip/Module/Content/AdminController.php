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
        \Ip\Module\Content\Service::setManagementMode(1);
        return (new \Ip\Response\Redirect(ipHomeUrl()));
    }

    public function setManagementMode()
    {
        Service::setManagementMode(intval(ipRequest()->getPost('value', 1)));
        return new \Ip\Response\Json(array(1));
    }

    public function getSitemapInList()
    {
        $answer = '';
        $answer .= '<ul id="ipSitemap">' . "\n";

        $answer .= '<li><a href="' . ipHomeUrl() . '">Home</a></li>' . "\n";

        $languages = \Ip\Internal\ContentDb::getLanguages(true); //get all languages including hidden

        foreach ($languages as $language) {
            $link = \Ip\Internal\Deprecated\Url::generate($language['id']);
            $answer .= '<li><a href="' . $link . '">' . htmlspecialchars($language['d_long']) . ' (' . htmlspecialchars(
                    $language['d_short']
                ) . ')</a>' . "\n";

            $zones = ipContent()->getZones();
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

        return new \Ip\Response($answer);
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


    public function getPageOptionsHtml()
    {
        if (!isset($_REQUEST['pageId'])) {
            return $this->_errorAnswer('Page id is not set');
        }

        $pageId = (int)$_REQUEST['pageId'];

        if (!isset($_REQUEST['zoneName'])) {
            return $this->_errorAnswer('Zone name is not set');
        }

        $zone = ipContent()->getZone($_REQUEST['zoneName']);

        if (!($zone)) {
            return $this->_errorAnswer('Can\'t find zone');
        }

        $element = $zone->getPage($pageId);

        if (!$element) {
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
        $data['layout'] = \Ip\Module\Content\Service::getPageLayout($page);
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
            $layouts = $widgetObject->getLooks();
            $widgetObject = Model::getWidgetObject($widgetName);
            $widgetId = Model::createWidget($widgetName, $widgetObject->defaultData(), $layouts[0]['name'], null);
            $instanceId = Model::addInstance($widgetId, $revisionId, $blockName, $position, true);
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
            \Ip\Module\Pages\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);
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


    public function savePageOptions()
    {
        if (empty($_POST['revisionId'])) {
            return $this->_errorAnswer('Missing revisionId POST variable');
        }
        $revisionId = $_POST['revisionId'];


        if (empty($_POST['pageOptions'])) {
            return $this->_errorAnswer('Missing pageOptions POST variable');
        }
        $pageOptions = $_POST['pageOptions'];

        $revision = \Ip\Revision::getRevision($revisionId);

        if (!$revision) {
            return $this->_errorAnswer('Can\'t find revision. RevisionId \'' . $revisionId . '\'');
        }

        $page = \Ip\Module\Pages\Db::getPage($revision['pageId']);
        if (isset($pageOptions['url']) && $pageOptions['url'] != $page['url']) {
            $changedUrl = true;
        } else {
            $changedUrl = false;
        }

        if ($changedUrl) {
            $zone = ipContent()->getZone($revision['zoneName']);
            $oldElement = $zone->getPage($revision['pageId']);
            $oldUrl = $oldElement->getLink();
        }

        \Ip\Module\Pages\Db::updatePage($revision['zoneName'], $revision['pageId'], $pageOptions);

        if ($changedUrl) {
            $newElement = $zone->getPage($revision['pageId']);
            $newUrl = $newElement->getLink();
        }

        $data = array(
            'status' => 'success',
            'action' => '_savePageOptionsResponse',
            'pageOptions' => $pageOptions,
        );

        if ($changedUrl) {
            $data['oldUrl'] = $oldUrl;
            $data['newUrl'] = $newUrl;
        }


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
