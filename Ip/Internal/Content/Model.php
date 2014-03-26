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
            'id' => null,
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

        $widgetData = $widgetRecord['data'];
        if (!is_array($widgetData)) {
            $widgetData = array();
        }


        $previewHtml = $widgetObject->generateHtml($widgetRecord['revisionId'], $widgetRecord['id'], $widgetData, $widgetRecord['skin']);

        $widgetRecord['data'] = $widgetObject->dataForJs($widgetRecord['revisionId'], $widgetRecord['id'], $widgetData, $widgetRecord['skin']);


        $optionsMenu = array();

        if (count($widgetObject->getSkins()) > 1) {
            $optionsMenu[] = array(
                'title' => __('Skin', 'Ip-admin', false),
                'attributes' => array(
                    'class' => 'ipsSkin',
                    'data-skins' => json_encode($widgetObject->getSkins()),
                    'data-currentskin' => $widgetRecord['skin']
                )
            );
        }


        $optionsMenu = ipFilter('ipWidgetManagementMenu', $optionsMenu, $widgetRecord);

        $variables = array(
            'managementState' => $managementState,
            'html' => $previewHtml,
            'widgetData' => $widgetRecord['data'],
            'widgetInstanceId' => $widgetRecord['id'],
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
            SELECT *
            FROM
                ' . ipTable('widget', 'w') . '
            WHERE
                `isDeleted` = 0 AND
                `blockName` = :blockName AND
                `revisionId` = :revisionId AND
                `languageId` = :languageId
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
                ' . ipTable('widget', 'i') . '
            WHERE
                i.revisionId = ? AND
                i.isDeleted = 0
            ORDER BY `position` ASC
        ';

        $instances = ipDb()->fetchAll($sql, array($oldRevisionId));

        foreach ($instances as $instance) {

            unset($instance['id']);
            $instance['revisionId'] = $newRevisionId;

            ipDb()->insert('widget', $instance);
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
            SELECT * FROM
                ' . ipTable('widget', 'w') . '
            WHERE
                `id` = ?
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
    public static function createWidget($widgetName, $data, $skin, $revisionId, $languageId, $blockName, $position, $visible = true)
    {
        $positionNumber = self::_calcWidgetPositionNumber($widgetName, $languageId, null, $blockName, $position);

        $row = array(
            'data' => json_encode(\Ip\Internal\Text\Utf8::checkEncoding($data)),
            'skin' => $skin,
            'name' => $widgetName,
            'revisionId' => $revisionId,
            'languageId' => $languageId,
            'blockName' => $blockName,
            'position' => $positionNumber,
            'isVisible' => (int)$visible,
            'createdAt' => time(),
            'updatedAt' => time(),
            'isDeleted' => 0,
        );

        return ipDb()->insert('widget', $row);
    }


    /**
     *
     * Return float number that will position widget in requested position
     * @param int $instnaceId
     * @param string $blockName
     * @param int $newPosition Real position of widget starting with 0
     */
    private static function _calcWidgetPositionNumber($revisionId, $languageId, $instanceId, $newBlockName, $newPosition)
    {
        $allWidgets = Model::getBlockWidgetRecords($newBlockName, $revisionId, $languageId);

        $widgets = array();

        foreach ($allWidgets as $instance) {
            if ($instanceId === null || $instance['id'] != $instanceId) {
                $widgets[] = $instance;
            }
        }

        if (count($widgets) == 0) {
            $positionNumber = 0;
        } else {
            if ($newPosition <= 0) {
                $positionNumber = $widgets[0]['position'] - 40;
            } else {
                if ($newPosition >= count($widgets)) {
                    $positionNumber = $widgets[count($widgets) - 1]['position'] + 40;
                } else {
                    $positionNumber = ($widgets[$newPosition - 1]['position'] + $widgets[$newPosition]['position']) / 2;
                }
            }
        }
        return $positionNumber;
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
        ipDb()->delete('widget', array('revisionId' => $revisionId));

        //TODOX execute widget's delete method
        ipdb()->delete('revision', array('revisionId' => $revisionId));
        //TODOX revision remove event
    }


    public static function removePageRevisions($pageId)
    {
        $revisions = self::getRevisions($pageId);
        foreach ($revisions as $revision) {
            self::removeRevision($revision['revisionId']);
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
        $table = ipTable('widget');
        //compare revision content
        $sql = "
            SELECT
                `id`
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
