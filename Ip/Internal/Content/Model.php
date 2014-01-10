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

    public static function generateBlock($blockName, $revisionId, $managementState, $exampleContent = '')
    {
        $widgets = self::getBlockWidgetRecords($blockName, $revisionId);

        $widgetsHtml = array();
        foreach ($widgets as $widget) {
            try {
                $widgetsHtml[] = self::_generateWidgetPreview($widget, $managementState);
            } catch (Exception $e) {
                throw new Exception('Error when generating widget preview', null, $e);
            }
        }

        $data = array(
            'widgetsHtml' => $widgetsHtml,
            'blockName' => $blockName,
            'revisionId' => $revisionId,
            'managementState' => $managementState,
            'exampleContent' => $exampleContent
        );
        $answer = ipView('view/block.php', $data)->render();
        return $answer;
    }

    public static function initManagementData()
    {

        $tmpWidgets = Model::getAvailableWidgetObjects();
        $tmpWidgets = Model::sortWidgets($tmpWidgets);
        $widgets = array();
        foreach ($tmpWidgets as $key => $widget) {
            if (!$widget->getUnderTheHood()) {
                $widgets[$key] = $widget;
            }
        }

        $revisions = \Ip\Internal\Revision::getPageRevisions(
            ipContent()->getCurrentZone()->getName(),
            ipContent()->getCurrentPage()->getId()
        );

        $managementUrls = array();
        $currentPageLink = ipContent()->getCurrentPage()->getLink();
        foreach ($revisions as $revision) {
            $managementUrls[] = $currentPageLink . '?cms_revision=' . $revision['revisionId'];
        }

        $revision = \Ip\ServiceLocator::content()->getCurrentRevision();

        $manageableRevision = isset($revisions[0]['revisionId']) && ($revisions[0]['revisionId'] == $revision['revisionId']);

        $page = ipContent()->getCurrentPage();

        $data = array(
            'widgets' => $widgets,
            'page' => $page,
            'revisions' => $revisions,
            'currentRevision' => $revision,
            'managementUrls' => $managementUrls,
            'manageableRevision' => $manageableRevision
        );

        $controlPanelHtml = ipView('view/control_panel.php', $data)->render();

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
        return ipDb()->select('*', 'm_developer_widget_sort', array(), 'ORDER BY `priority` ASC');
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
            'created' => time(),
            'recreated' => time(),
            'instanceId' => null,
            'revisionId' => null,
            'position' => null,
            'blockName' => null,
            'visible' => 1,
            'deleted' => null
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
        if ($themeChanged > $widgetRecord['recreated']) {
            $widgetData = $widgetRecord['data'];
            if (!is_array($widgetData)) {
                $widgetData = array();
            }

            $newData = $widgetObject->recreate($widgetRecord['instanceId'], $widgetData);
            self::updateWidget($widgetRecord['widgetId'], array('recreated' => time(), 'data' => $newData));
            $widgetRecord = self::getWidgetFullRecord($widgetRecord['instanceId']);
        }


        $widgetData = $widgetRecord['data'];
        if (!is_array($widgetData)) {
            $widgetData = array();
        }


        $previewHtml = $widgetObject->generateHtml($widgetRecord['revisionId'], $widgetRecord['widgetId'], $widgetRecord['instanceId'], $widgetData, $widgetRecord['layout']);

        $widgetRecord['data'] = $widgetObject->dataForJs($widgetRecord['data']);


        $optionsMenu = array();
        $optionsMenu[] = array(
            'title' => __('Look', 'ipAdmin', false),
            'attributes' => array(
                'class' => 'ipsLook',
                'data-layouts' => json_encode($widgetObject->getLooks()),
                'data-currentlayout' => $widgetRecord['layout']
            )
        );

        $optionsMenu = ipDispatcher()->filter('ipWidgetManagementMenu', $optionsMenu, $widgetRecord);
        $data = array(
            'optionsMenu' => $optionsMenu,
        );

        $widgetControlsHtml = ipView('view/widgetControls.php', $data)->render();


        $variables = array(
            'managementState' => $managementState,
            'html' => $previewHtml,
            'widgetData' => $widgetRecord['data'],
            'widgetInstanceId' => $widgetRecord['instanceId'],
            'widgetName' => $widgetRecord['name'],
            'widgetLayout' => $widgetRecord['layout'],
            'optionsMenu' => $optionsMenu,
            'widgetControlsHtml' => $widgetControlsHtml
        );

        $answer = ipView('view/widget.php', $variables)->render();
        return $answer;
    }


    public static function getBlockWidgetRecords($blockName, $revisionId)
    {
        $sql = '
            SELECT * 
            FROM
                ' . ipTable('widget_instance', 'i') . ',
                ' . ipTable('widget', 'w') . '
            WHERE
                i.deleted is NULL AND
                i.widgetId = w.widgetId AND
                i.blockName = :blockName AND
                i.revisionId = :revisionId
            ORDER BY `position` ASC
        ';

        $list = ipDb()->fetchAll($sql, array(
                'blockName' => $blockName,
                'revisionId' => $revisionId,
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
                i.deleted IS NULL
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

        self::$widgetObjects = ipDispatcher()->filter('ipWidgets', array());

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
            return new \Ip\Internal\Content\Widget\IpMissing\Controller('IpMissing', 'Content', true);
        }

    }

    public static function getWidgetRecord($widgetId)
    {
        $rs = ipDb()->select('*', 'widget', array('widgetId' => $widgetId));

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
                i.widgetId = w.widgetId 
        ';
        $row = ipDb()->fetchRow($sql, array($instanceId));
        if (!$row) {
            return null;
        }

        $row['data'] = json_decode($row['data'], true);
        return $row;
    }

    public static function getRevisions($zoneName, $pageId)
    {
        return ipDb()->select('*', 'revision', array('zoneName' => $zoneName, 'pageId' => $pageId));
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
                'created' => time(),
                'recreated' => time(),
                'data' => json_encode(\Ip\Internal\Text\Utf8::checkEncoding($data))
            ));
    }


    public static function updateWidget($widgetId, $data)
    {
        if (array_key_exists('data', $data)) {
            $data['data'] = json_encode(\Ip\Internal\Text\Utf8::checkEncoding($data['data']));
        }

        return ipDb()->update('widget', $data, array('widgetId' => $widgetId));
    }




    public static function removeRevision($revisionId)
    {
        ipDb()->delete('widget_instance', array('revisionId' => $revisionId));
        ipdb()->delete('revision', array('revisionId' => $revisionId));
    }


    public static function removePageRevisions($zoneName, $pageId)
    {
        $revisions = self::getRevisions($zoneName, $pageId);
        foreach ($revisions as $revisionKey => $revision) {
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
            SELECT w.widgetId
            FROM " . ipTable('widget', 'w') . "
            LEFT JOIN " . ipTable('widget_instance', 'i') . "
            ON i.widgetId = w.widgetId
            WHERE i.instanceId IS NULL
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

        ipDb()->delete('widget', array('widgetId' => $widgetId));
    }


    public static function clearCache($revisionId)
    {

        $revision = \Ip\Internal\Revision::getRevision($revisionId);
        $pageContent = Model::generateBlock('main', $revisionId, false);

        $html2text = new \Ip\Internal\Text\Html2Text();
        $html2text->set_html($pageContent);
        $pageContentText = $html2text->get_text();

        $params = array(
            'cached_html' => $pageContent,
            'cached_text' => $pageContentText
        );
        \Ip\Internal\Pages\Db::updatePage($revision['zoneName'], $revision['pageId'], $params);
    }




}