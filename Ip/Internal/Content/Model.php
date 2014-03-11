<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;


class Model
{
    static private $widgetObjects = null;
    const DEFAULT_SKIN = 'default';
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

        $revision = \Ip\ServiceLocator::content()->getCurrentRevision();

        $revisions = \Ip\Internal\Revision::getPageRevisions(ipContent()->getCurrentPage()->getId());

        $manageableRevision = isset($revisions[0]['revisionId']) && ($revisions[0]['revisionId'] == $revision['revisionId']);

        $page = ipContent()->getCurrentPage();

        unset($widgets['Columns']);

        $data = array(
            'widgets' => $widgets,
            'page' => $page,
            'currentRevision' => $revision,
            'manageableRevision' => $manageableRevision
        );

        $controlPanelHtml = ipView('view/adminPanel.php', $data)->render();

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

    public static function generateWidgetPreviewFromStaticData($widgetName, $data, $skin = null)
    {
        if ($skin == null) {
            $skin = self::DEFAULT_SKIN;
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
            'skin' => $skin,
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


        $previewHtml = $widgetObject->generateHtml($widgetRecord['revisionId'], $widgetRecord['widgetId'], $widgetRecord['instanceId'], $widgetData, $widgetRecord['skin']);

        $widgetRecord['data'] = $widgetObject->dataForJs($widgetRecord['revisionId'], $widgetRecord['widgetId'], $widgetRecord['instanceId'], $widgetData, $widgetRecord['skin']);


        $optionsMenu = array();

        if (count($widgetObject->getSkins()) > 1) {
            $optionsMenu[] = array(
                'title' => __('Skin', 'ipAdmin', false),
                'attributes' => array(
                    'class' => 'ipsSkin',
                    'data-skins' => json_encode($widgetObject->getSkins()),
                    'data-currentskin' => $widgetRecord['skin']
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
            'widgetSkin' => $widgetRecord['skin'],
            'optionsMenu' => $optionsMenu
        );

        $answer = ipView('view/widget.php', $variables)->render();
        return $answer;
    }


    public static function getBlockWidgetRecords($blockName, $revisionId, $languageId)
    {
        $sql = '
            SELECT i.*,
                i.id AS `instanceId`,
                w.id AS `widgetId`,
                w.name AS `name`,
                w.skin AS `skin`,
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
                'languageId' => $languageId
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
                ' . ipTable('widgetInstance', 'i') . '
            WHERE
                i.revisionId = ? AND
                i.isDeleted = 0
            ORDER BY `position` ASC
        ';

        $instances = ipDb()->fetchAll($sql, array($oldRevisionId));

        foreach ($instances as $instance) {

            unset($instance['id']);
            $instance['revisionId'] = $newRevisionId;

            ipDb()->insert('widgetInstance', $instance);
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
     * getWidgetFullRecord differ from getWidgetRecord by including the information from widgetInstance table.
     * @param int $instanceId
     * @throws Exception
     */
    public static function getWidgetFullRecord($instanceId)
    {
        $sql = '
            SELECT *, i.id as instanceId FROM
                ' . ipTable('widgetInstance', 'i') . ',
                ' . ipTable('widget', 'w') . '
            WHERE
                i.`id` = ? AND
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







    /**
     *
     * Enter description here ...
     * @param int $revisionId
     * @param int $position Real position of widget starting with 0
     * @param string $blockName
     * @param string $widgetName
     * @param string $skin
     * @throws Exception
     */
    public static function createWidget($widgetName, $data, $skin)
    {
        return ipDb()->insert('widget', array(
                'name' => $widgetName,
                'skin' => $skin,
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
        ipDb()->delete('widgetInstance', array('revisionId' => $revisionId));
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
            SELECT `widget`.`id`
            FROM " . ipTable('widget', 'widget') . "
            LEFT JOIN " . ipTable('widgetInstance', 'widgetInstance') . "
            ON `widgetInstance`.`widgetId` = `widget`.`id`
            WHERE `widgetInstance`.`id` IS NULL
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

    public static function updateUrl($oldUrl, $newUrl)
    {
        $oldUrl = str_replace('\/', '\\\/', $oldUrl);
        $newUrl = str_replace('\/', '\\\/', $newUrl);
        $dbh = ipDb()->getConnection();
        $table = ipTable('widget');
        $sql = "
            UPDATE
              $table
            SET
              `data` = REPLACE(`data`, :oldUrl, :newUrl)
            WHERE
                1
        ";

        $params = array (
            ':oldUrl' => $oldUrl,
            ':newUrl' => $newUrl
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    public static function isRevisionModified($revisionId = null)
    {
        if ($revisionId === null) {
            $currentRevision = ipContent()->getCurrentRevision();
            $revisionId = $currentRevision['revisionId'];
        }

        $currentRevision = \Ip\Internal\Revision::getRevision($revisionId);
        if (!$currentRevision) {
            return FALSE;
        }
        $pageId = $currentRevision['pageId'];

        $publishedRevision = \Ip\Internal\Revision::getPublishedRevision($pageId);
        if (!$publishedRevision) {
            return TRUE;
        }

        if ($publishedRevision['revisionId'] == $currentRevision['revisionId']) {
            return FALSE;
        }

        $currentWidgetIds = self::revisionWidgetIds($currentRevision['revisionId']);
        $publishedWidgetIds = self::revisionWidgetIds($publishedRevision['revisionId']);
        $currentFingerprint = implode(',', $currentWidgetIds);
        $publishedFingerprint = implode(',', $publishedWidgetIds);

        $modified = $currentFingerprint != $publishedFingerprint;

        return $modified;
    }


    protected static function revisionWidgetIds($revisionId)
    {
        $table = ipTable('widgetInstance');
        //compare revision content
        $sql = "
            SELECT
                `widgetId`
            FROM
                $table
            WHERE
              `revisionId` = :revisionId
            ORDER BY
              blockName, `position`
        ";

        $params = array(
            'revisionId' => $revisionId
        );

        $widgetIds = ipDb()->fetchColumn($sql, $params);
        return $widgetIds;
    }


}
