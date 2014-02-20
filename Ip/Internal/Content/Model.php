<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;


class Model
{
    static private $widgetObjects = null;
    const DEFAULT_LAYOUT = 'default';
    const WIDGET_DIR = 'Widget';
    const SNIPPET_DIR = 'snippet';

    public static function generateBlock($blockName, $revisionId, $languageId, $managementState, $exampleContent = '')
    {
        $widgets = self::getBlockWidgetRecords($blockName, $revisionId, $languageId);

        $widgetsHtml = array();
        foreach ($widgets as $widget) {
            try {
                $widgetsHtml[] = self::_generateWidgetPreview($widget, $managementState);
            } catch (Exception $e) {
                throw new Exception('Error when generating widget preview', null, $e);
            }
        }

        $variables = array(
            'widgetsHtml' => $widgetsHtml,
            'blockName' => $blockName,
            'revisionId' => $revisionId,
            'languageId' => $languageId,
            'managementState' => $managementState,
            'exampleContent' => $exampleContent
        );
        $answer = ipView('view/block.php', $variables)->render();
        return $answer;
    }

    public static function initManagementData()
    {

        $tmpWidgets = Model::getAvailableWidgetObjects();
        $tmpWidgets = Model::sortWidgets($tmpWidgets);
        $widgets = array();
        foreach ($tmpWidgets as $key => $widget) {
            $widgets[$key] = $widget;
        }

        $revisions = \Ip\Internal\Revision::getPageRevisions(ipContent()->getCurrentPage()->getId());

        $managementUrls = array();
        $currentPageLink = ipContent()->getCurrentPage()->getLink();
        foreach ($revisions as $revision) {
            $managementUrls[] = $currentPageLink . '?cms_revision=' . $revision['revisionId'];
        }

        $revision = \Ip\ServiceLocator::content()->getCurrentRevision();

        $manageableRevision = isset($revisions[0]['revisionId']) && ($revisions[0]['revisionId'] == $revision['revisionId']);

        $page = ipContent()->getCurrentPage();

        unset($widgets['Columns']);

        $data = array(
            'widgets' => $widgets,
            'page' => $page,
            'revisions' => $revisions,
            'currentRevision' => $revision,
            'managementUrls' => $managementUrls,
            'manageableRevision' => $manageableRevision
        );

        $controlPanelHtml = ipView('view/controlPanel.php', $data)->render();

        $data = array(
            'controlPanelHtml' => $controlPanelHtml,
            'manageableRevision' => $manageableRevision
        );

        return $data;
    }

    public static function sortWidgets($widgets)
    {
        $priorities = self::_getPriorities();
        $sortedWidgets = array();
        $unsortedWidgets = array();
        foreach ($widgets as $widget) {
            if (isset($priorities[$widget->getName()])) {
                $position = $priorities[$widget->getName()];
                $sortedWidgets[(int)$position] = $widget;
            } else {
                $unsortedWidgets[] = $widget;
            }
        }
        ksort($sortedWidgets);
        $answer = array();
        foreach ($sortedWidgets as $widget) {
            $answer[$widget->getName()] = $widget;
        }

        foreach ($unsortedWidgets as $widget) {
            $answer[$widget->getName()] = $widget;
        }

        return $answer;
    }

    private static function _getPriorities()
    {
        $list = ipDb()->selectAll('widgetOrder', '*', array(), 'ORDER BY `priority` ASC');
        $result = array();
        foreach ($list as $widgetOrder) {
            $result[$widgetOrder['widgetName']] = $widgetOrder['priority'];
        }

        return $result;
    }

    public static function generateWidgetPreviewFromStaticData($widgetName, $data, $layout = null)
    {
        if ($layout == null) {
            $layout = self::DEFAULT_LAYOUT;
        }
        $widgetObject = self::getWidgetObject($widgetName);
        if (!$widgetObject) {
            $backtrace = debug_backtrace();
            if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                $source = ' (Error source: ' . $backtrace[0]['file'] . ' line: ' . $backtrace[0]['line'] . ' ) ';
            } else {
                $source = '';
            }

            throw new Exception('Widget ' . $widgetName . ' does not exist. ' . $source, Exception::UNKNOWN_WIDGET);
        }

        $widgetRecord = array(
            'widgetId' => null,
            'name' => $widgetName,
            'layout' => $layout,
            'data' => $data,
            'createdAt' => time(),
            'updatedAt' => time(),
            'instanceId' => null,
            'revisionId' => null,
            'position' => null,
            'blockName' => null,
            'isVisible' => 1,
        );
        return self::_generateWidgetPreview($widgetRecord, false);

    }


    public static function generateWidgetPreview($instanceId, $managementState)
    {
        $widgetRecord = self::getWidgetFullRecord($instanceId);
        return self::_generateWidgetPreview($widgetRecord, $managementState);
    }



    private static function _generateWidgetPreview($widgetRecord, $managementState)
    {
        $widgetObject = self::getWidgetObject($widgetRecord['name']);
        //check if we don't need to recreate the widget
        $themeChanged = \Ip\ServiceLocator::storage()->get('Ip', 'themeChanged');


        $widgetData = $widgetRecord['data'];
        if (!is_array($widgetData)) {
            $widgetData = array();
        }


        $previewHtml = $widgetObject->generateHtml($widgetRecord['revisionId'], $widgetRecord['widgetId'], $widgetRecord['instanceId'], $widgetData, $widgetRecord['layout']);

        $widgetRecord['data'] = $widgetObject->dataForJs($widgetRecord['revisionId'], $widgetRecord['widgetId'], $widgetRecord['instanceId'], $widgetData, $widgetRecord['layout']);


        $optionsMenu = array();

        if (count($widgetObject->getSkins()) > 1) {
            $optionsMenu[] = array(
                'title' => __('Skin', 'ipAdmin', false),
                'attributes' => array(
                    'class' => 'ipsSkin',
                    'data-layouts' => json_encode($widgetObject->getSkins()),
                    'data-currentlayout' => $widgetRecord['layout']
                )
            );
        }


        $optionsMenu = ipFilter('ipWidgetManagementMenu', $optionsMenu, $widgetRecord);
        $data = array(
            'optionsMenu' => $optionsMenu,
        );

        $variables = array(
            'managementState' => $managementState,
            'html' => $previewHtml,
            'widgetData' => $widgetRecord['data'],
            'widgetInstanceId' => $widgetRecord['instanceId'],
            'widgetName' => $widgetRecord['name'],
            'widgetLayout' => $widgetRecord['layout'],
            'optionsMenu' => $optionsMenu
        );

        $answer = ipView('view/widget.php', $variables)->render();
        return $answer;
    }


    public static function getBlockWidgetRecords($blockName, $revisionId, $languageId)
    {
        $sql = '
            SELECT i.*,
                w.id AS `widgetId`,
                w.name AS `name`,
                w.layout AS `layout`,
                w.data AS `data`,
                w.updatedAt AS `updatedAt`
            FROM
                ' . ipTable('widgetInstance', 'i') . ',
                ' . ipTable('widget', 'w') . '
            WHERE
                i.isDeleted = 0 AND
                i.widgetId = w.id AND
                i.blockName = :blockName AND
                i.revisionId = :revisionId AND
                i.languageId = :languageId
            ORDER BY `position` ASC
        ';

        $list = ipDb()->fetchAll($sql, array(
                'blockName' => $blockName,
                'revisionId' => $revisionId,
                'languageId' => $languageId,
            ));

        foreach ($list as &$item) {
            $item['data'] = json_decode($item['data'], true);
        }

        return $list;
    }

    public static function duplicateRevision($oldRevisionId, $newRevisionId)
    {
        $sql = '
            SELECT *
            FROM
                ' . ipTable('widget_instance', 'i') . '
            WHERE
                i.revisionId = ? AND
                i.isDeleted = 0
            ORDER BY `position` ASC
        ';

        $instances = ipDb()->fetchAll($sql, array($oldRevisionId));

        foreach ($instances as $instance) {

            unset($instance['instanceId']);
            $instance['revisionId'] = $newRevisionId;

            ipDb()->insert('widget_instance', $instance);
        }

    }

    /**
     * @return \Ip\WidgetController[]
     */
    public static function getAvailableWidgetObjects()
    {

        if (self::$widgetObjects !== null) {
            return self::$widgetObjects;
        }

        self::$widgetObjects = ipFilter('ipWidgets', array());

        return self::$widgetObjects;
    }

    /**
     *
     * @param string $widgetName
     * @return \Ip\WidgetController
     */
    public static function getWidgetObject($widgetName)
    {
        $widgetObjects = self::getAvailableWidgetObjects();

        if (isset($widgetObjects[$widgetName])) {
            return $widgetObjects[$widgetName];
        } else {
            return new \Ip\Internal\Content\Widget\Missing\Controller('Missing', 'Content', true);
        }

    }

    public static function getWidgetRecord($widgetId)
    {
        $rs = ipDb()->selectAll('widget', '*', array('id' => $widgetId));

        if ($rs) {
            $rs[0]['data'] = json_decode($rs[0]['data'], true);
            return $rs[0];
        } else {
            return null;
        }
    }

    /**
     *
     * getWidgetFullRecord differ from getWidgetRecord by including the information from widget_instance table.
     * @param int $instanceId
     * @throws Exception
     */
    public static function getWidgetFullRecord($instanceId)
    {
        $sql = '
            SELECT * FROM
                ' . ipTable('widget_instance', 'i') . ',
                ' . ipTable('widget', 'w') . '
            WHERE
                i.`instanceId` = ? AND
                i.widgetId = w.id
        ';
        $row = ipDb()->fetchRow($sql, array($instanceId));
        if (!$row) {
            return null;
        }

        $row['data'] = json_decode($row['data'], true);
        return $row;
    }

    public static function getRevisions($pageId)
    {
        return ipDb()->selectAll('revision', '*', array('pageId' => $pageId));
    }

    public static function updatePageRevisionsZone($pageId, $oldZoneName, $newZoneName)
    {
        return ipDb()->update(
            'revision',
            array(
                'zoneName' => $newZoneName,
            ),
            array(
                'pageId' => $pageId,
                'zoneName' => $oldZoneName,
            )
        );
    }





    /**
     *
     * Enter description here ...
     * @param int $revisionId
     * @param int $position Real position of widget starting with 0
     * @param string $blockName
     * @param string $widgetName
     * @param string $layout
     * @throws Exception
     */
    public static function createWidget($widgetName, $data, $layout)
    {
        return ipDb()->insert('widget', array(
                'name' => $widgetName,
                'layout' => $layout,
                'createdAt' => time(),
                'updatedAt' => time(),
                'data' => json_encode(\Ip\Internal\Text\Utf8::checkEncoding($data))
            ));
    }


    public static function updateWidget($widgetId, $data)
    {
        if (array_key_exists('data', $data)) {
            $data['data'] = json_encode(\Ip\Internal\Text\Utf8::checkEncoding($data['data']));
        }

        return ipDb()->update('widget', $data, array('id' => $widgetId));
    }




    public static function removeRevision($revisionId)
    {
        ipDb()->delete('widget_instance', array('revisionId' => $revisionId));
        ipdb()->delete('revision', array('revisionId' => $revisionId));
    }


    public static function removePageRevisions($pageId)
    {
        $revisions = self::getRevisions($pageId);
        foreach ($revisions as $revision) {
            self::removeRevision($revision['revisionId']);
        }

        self::deleteUnusedWidgets();
    }


    /**
     *
     * Each widget might be used many times. That is controlled using instanaces. This method destroys all widgets that has no instances.
     * @throws Exception
     */
    public static function deleteUnusedWidgets()
    {
        $sql = "
            SELECT `widget`.id
            FROM " . ipTable('widget') . "
            LEFT JOIN " . ipTable('widget_instance') . "
            ON widget_instance.widgetId = widget.id
            WHERE widget_instance.instanceId IS NULL
        ";

        $db = ipDb();

        $widgetList = $db->fetchColumn($sql);

        foreach ($widgetList as $widgetId) {
            self::deleteWidget($widgetId);
        }
    }

    /**
     *
     * Completely remove widget.
     * @param int $widgetId
     */
    public static function deleteWidget($widgetId)
    {
        $widgetRecord = self::getWidgetRecord($widgetId);
        $widgetObject = self::getWidgetObject($widgetRecord['name']);

        if ($widgetObject) {
            $widgetObject->delete($widgetId, $widgetRecord['data']);
        }

        ipDb()->delete('widget', array('id' => $widgetId));
    }


    public static function clearCache($revisionId)
    {

//        $revision = \Ip\Internal\Revision::getRevision($revisionId);
//        $pageContent = Model::generateBlock('main', $revisionId, false);
//
//        $html2text = new \Ip\Internal\Text\Html2Text();
//        $html2text->set_html($pageContent);
//        $pageContentText = $html2text->get_text();

//        $params = array(
//            'cached_html' => $pageContent,
//            'cached_text' => $pageContentText
//        );
//        \Ip\Internal\Pages\Db::updatePage($revision['pageId'], $params);
    }




}
