<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Module\Content;


use Ip\Form\Exception;

class Service
{


    /**
     * @return \Ip\WidgetController[]
     */
    public static function getAvailableWidgets()
    {
        return Model::getAvailableWidgetObjects();
    }


    public static function setManagementMode($newMode)
    {
        $_SESSION['Content']['managementMode'] = $newMode ? 1 : 0;
    }

    public static function isManagementMode()
    {
        return !empty($_SESSION['Content']['managementMode']);
    }

    public static function getPageLayout(\Ip\Page $page)
    {
        $zone = ipContent()->getZone($page->getZoneName());
        $layout = \Ip\Internal\ContentDb::getPageLayout(
            $zone->getAssociatedModuleGroup(),
            $zone->getAssociatedModule(),
            $page->getId()
        );


        if (!$layout) {
            $layout = $zone->getLayout();
        }
        return $layout;
    }

    public static function addWidget(
        $widgetName,
        $zoneName,
        $pageId,
        $blockName = null,
        $revisionId = null,
        $position = null
    ) {
        if (is_null($revisionId)) {
            //Static block;
            //TODOX use \Ip\Revision::getLastRevision instead
            $revisionId = \Ip\Revision::createRevision($zoneName, $pageId, true);
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
                //TODOX service must not return Response object.
                return self::_errorAnswer('Unknown zone "' . $zoneName . '"');
            }

            $page = $zone->getPage($pageId);
            if ($page === false) {
                //TODOX service must not return Response object.
                return self::_errorAnswer('Page not found "' . $zoneName . '"/"' . $pageId . '"');
            }

        }

        $widgetObject = Model::getWidgetObject($widgetName);

        if ($widgetObject === false) {
            //TODOX service must not return Response object.
            return self::_errorAnswer('Unknown widget "' . $widgetName . '"');
        }

        try {

            $layouts = $widgetObject->getLayouts();
            $widgetId = Model::createWidget($widgetName, array(), $layouts[0]['name'], null);

        } catch (Exception $e) {
            //TODOX service must not return Response object.
            return self::_errorAnswer($e);
        }

        try {
            $instanceId = Model::addInstance($widgetId, $revisionId, $blockName, $position, true);
        } catch (Exception $e) {
            //TODOX service must not return Response object.
            return self::_errorAnswer($e);
        }
        return $instanceId;

    }


    public static function addWidgetContent($instanceId, $content, $layout = 'default')
    {

        try{
            $record = Model::getWidgetFullRecord($instanceId);
            $widgetObject = Model::getWidgetObject($record['name']);
            $newData = $widgetObject->update($record['widgetId'], $content, $record['data']);
            $updateArray = array(
                'data' => $newData,
                'layout' => $layout
            );

            Model::updateWidget($record['widgetId'], $updateArray);
        }catch (Exception $e){
            return self::_errorAnswer($e);
        }

    }

    public static function addPage(
        $zoneName,
        $parentPageId,
        $buttonTitle = 'page',
        $pageTitle = 'Page',
        $url = null,
        $position = null
    ) {

        $zone = ipContent()->getZone($zoneName);

        $parentPage = $zone->getPage($parentPageId);

        $data = array();

        $data['buttonTitle'] = $buttonTitle;
        $data['pageTitle'] = $pageTitle;

        if (!is_null($url)) {
            $data['url'] = \Ip\Module\Pages\Db::makeUrl($url);
        }

        $data['createdOn'] = date("Y-m-d");
        $data['lastModified'] = date("Y-m-d");
        $data['visible'] = !ipGetOption('Pages.hideNewPages');

        $newPageId =  \Ip\Module\Pages\Db::insertPage($parentPage->getId(), $data);

        return $newPageId;
    }

    public static function removePage($pageId)
    {

    }


    private static function _errorAnswer($errorMessage)
    {

        $data = array(
            'status' => 'error',
            'errorMessage' => $errorMessage
        );

        // TODO use jsonrpc response
        return new \Ip\Response\Json($data);
    }
}