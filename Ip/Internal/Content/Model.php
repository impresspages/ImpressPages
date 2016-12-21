<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Content;


use Ip\Internal\Browser;

class Model
{

    static private $widgetObjects = null;
    const DEFAULT_SKIN = 'default';
    const WIDGET_DIR = 'Widget';
    const SNIPPET_DIR = 'snippet';

    /**
     * @param string $blockName
     * @param int $revisionId
     * @param int $languageId
     * @param bool $managementState
     * @param string $exampleContent
     * @throws \Ip\Exception\Content
     * @return string
     */
    public static function generateBlock($blockName, $revisionId, $languageId, $managementState, $exampleContent = '')
    {
        $widgets = self::getBlockWidgetRecords($blockName, $revisionId, $languageId);

        $widgetsHtml = [];
        foreach ($widgets as $widget) {
            try {
                $widgetsHtml[] = self::_generateWidgetPreview($widget, $managementState);
            } catch (\Ip\Exception\Content $e) {
                throw new \Ip\Exception\Content('Error when generating widget preview', null, $e);
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

    /**
     * @param int $widgetId
     * @param int $position
     * @param string $blockName
     * @param int $revisionId
     * @param int $languageId
     */
    public static function moveWidget($widgetId, $position, $blockName, $revisionId, $languageId)
    {
        $positionNumber = self::_calcWidgetPositionNumber($revisionId, $languageId, $widgetId, $blockName, $position);
        $data = array(
            'position' => $positionNumber,
            'languageId' => $languageId,
            'blockName' => $blockName,
            'revisionId' => $revisionId
        );
        $eventData = $data;
        $eventData['widgetId'] = $widgetId;

        ipEvent('ipBeforeWidgetMove', $eventData);
        self::updateWidget($widgetId, $data);
        ipEvent('ipAfterWidgetMove', $eventData);
    }

    /**
     * @return array
     */
    public static function initManagementData()
    {
        $tmpWidgets = Model::getAvailableWidgetObjects();
        $tmpWidgets = Model::sortWidgets($tmpWidgets);
        $tags = array(
            'Core' => []
        );
        $uncategorizedWidgets = [];

        unset($tmpWidgets['Columns']);

        foreach ($tmpWidgets as $key => $widget) {
            if ($widget->isCore()) {
                $tags['Core'][$key] = $widget->getName();
            } else {
                $pluginName = $widget->getPluginName();
                if (!array_key_exists($pluginName, $tags)) {
                    $tags[$pluginName] = [];
                }
                $tags[$pluginName][] = $widget->getName();
            }
        }

        // Filter out single widget categories
        foreach ($tags as $key => $widget) {
            $widgetCount = count($tags[$key]);

            if ($widgetCount === 1) {
                $uncategorizedWidgets[] = $widget[0];
                unset($tags[$key]);
            }
        }

        if (count($uncategorizedWidgets) > 0) {
            $tags['Other'] = $uncategorizedWidgets;
        }

        //these two tranlsations appear here just to make translations engine to find these dynamic translations
        __('Other', 'Ip-admin', false);
        __('Core', 'Ip-admin', false);

        $revision = \Ip\ServiceLocator::content()->getCurrentRevision();

        $revisions = \Ip\Internal\Revision::getPageRevisions(ipContent()->getCurrentPage()->getId());

        $manageableRevision = isset($revisions[0]['revisionId']) && ($revisions[0]['revisionId'] == $revision['revisionId']);

        $page = ipContent()->getCurrentPage();

        $tags = ipFilter('ipAdminWidgets', $tags);


        $data = array(
            'widgets' => $tmpWidgets,
            'tags' => $tags,
            'page' => $page,
            'currentRevision' => $revision,
            'manageableRevision' => $manageableRevision,
            'categorySplit' => 3,
            'mobile' => Browser::isMobile()
        );

        $controlPanelHtml = ipView('view/adminPanel.php', $data)->render();

        $data = array(
            'tags' => $tags,
            'controlPanelHtml' => $controlPanelHtml,
            'manageableRevision' => $manageableRevision
        );

        return $data;
    }

    /**
     * @param array $widgets
     * @return array
     */
    public static function sortWidgets($widgets)
    {
        $priorities = self::_getPriorities();
        $sortedWidgets = [];
        $unsortedWidgets = [];
        foreach ($widgets as $widget) {
            if (isset($priorities[$widget->getName()])) {
                $position = $priorities[$widget->getName()];
                $sortedWidgets[(int)$position] = $widget;
            } else {
                $unsortedWidgets[] = $widget;
            }
        }
        ksort($sortedWidgets);
        $answer = [];
        foreach ($sortedWidgets as $widget) {
            $answer[$widget->getName()] = $widget;
        }

        foreach ($unsortedWidgets as $widget) {
            $answer[$widget->getName()] = $widget;
        }

        return $answer;
    }

    /**
     * @return array
     */
    private static function _getPriorities()
    {
        $list = ipDb()->selectAll('widget_order', '*', [], 'ORDER BY `priority` ASC');
        $result = [];
        foreach ($list as $widgetOrder) {
            $result[$widgetOrder['widgetName']] = $widgetOrder['priority'];
        }

        return $result;
    }

    /**
     * @param string $widgetName
     * @param string $data
     * @param string $skin
     * @throws \Ip\Exception\Content
     * @return string
     */
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

            throw new \Ip\Exception\Content('Widget ' . esc(
                $widgetName
            ) . ' does not exist.', array('widgetName' => $widgetName, 'source' => $source));
        }

        $widgetRecord = array(
            'id' => null,
            'name' => $widgetName,
            'skin' => $skin,
            'data' => $data,
            'createdAt' => time(),
            'updatedAt' => time(),
            'revisionId' => null,
            'position' => null,
            'blockName' => null,
            'isVisible' => 1,
        );

        return self::_generateWidgetPreview($widgetRecord, false);
    }

    /**
     * @param int $widgetId
     * @param $managementState
     * @return string
     */
    public static function generateWidgetPreview($widgetId, $managementState)
    {
        $widgetRecord = self::getWidgetRecord($widgetId);
        return self::_generateWidgetPreview($widgetRecord, $managementState);
    }

    /**
     * @param array $widgetRecord
     * @param $managementState
     * @return string
     */
    private static function _generateWidgetPreview($widgetRecord, $managementState)
    {
        $widgetObject = self::getWidgetObject($widgetRecord['name']);

        $widgetData = $widgetRecord['data'];
        if (!is_array($widgetData)) {
            $widgetData = [];
        }

        if (!$widgetRecord['revisionId']) {
            $currentRevision = ipContent()->getCurrentRevision();
            $widgetRecord['revisionId'] = $currentRevision['revisionId'];
        }

        $previewHtml = $widgetObject->generateHtml(
            $widgetRecord['revisionId'],
            $widgetRecord['id'],
            $widgetData,
            $widgetRecord['skin']
        );

        $widgetRecord['data'] = $widgetObject->dataForJs(
            $widgetRecord['revisionId'],
            $widgetRecord['id'],
            $widgetData,
            $widgetRecord['skin']
        );

        $optionsMenu = [];


        $previewHtml = ipFilter('ipWidgetHtml', $previewHtml, $widgetRecord);


        $variables = array(
            'managementState' => $managementState,
            'html' => $previewHtml,
            'widgetData' => $widgetRecord['data'],
            'widgetId' => $widgetRecord['id'],
            'widgetName' => $widgetRecord['name'],
            'widgetSkin' => $widgetRecord['skin']
        );

        if ($managementState) {
            $skins = $widgetObject->getSkins();
            if (count($skins) > 1) {
                $optionsMenu[] = array(
                    'title' => __('Skin', 'Ip-admin', false),
                    'attributes' => array(
                        'class' => 'ipsSkin',
                        'data-skins' => json_encode($skins),
                        'data-currentskin' => $widgetRecord['skin']
                    )
                );
            }
            $widgetOptions =  $widgetObject->optionsMenu(
                $widgetRecord['revisionId'],
                $widgetRecord['id'],
                $widgetData,
                $widgetRecord['skin']
            );
            $optionsMenu = array_merge($optionsMenu, $widgetOptions);
            $optionsMenu = ipFilter('ipWidgetManagementMenu', $optionsMenu, $widgetRecord);
            $variables['optionsMenu'] = $optionsMenu;
        }


        $answer = ipView('view/widget.php', $variables)->render();

        $answer = ipFilter('ipWidgetHtmlFull', $answer, $widgetRecord);

        return $answer;
    }

    /**
     * @param string $blockName
     * @param int $revisionId
     * @param int $languageId
     * @return array
     */
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

        $list = ipDb()->fetchAll(
            $sql,
            array(
                'blockName' => $blockName,
                'revisionId' => $revisionId,
                'languageId' => $languageId
            )
        );

        foreach ($list as &$item) {
            $item['data'] = json_decode($item['data'], true);
        }

        return $list;
    }

    /**
     * @param int $oldRevisionId
     * @param int $newRevisionId
     */
    public static function duplicateRevision($oldRevisionId, $newRevisionId)
    {
        $widgetTable = ipTable('widget');

        $sql = "
            SELECT *
            FROM
                $widgetTable
            WHERE
                `revisionId` = ? AND
                `isDeleted` = 0
            ORDER BY `position` ASC
        ";

        $widgets = ipDb()->fetchAll($sql, array($oldRevisionId));

        $widgetIdTransition = [];
        foreach ($widgets as $widget) {
            $widgetObject = Model::getWidgetObject($widget['name']);

            $oldWidgetId = $widget['id'];
            unset($widget['id']);
            $widget['revisionId'] = $newRevisionId;

            $newWidgetId = ipDb()->insert('widget', $widget);

            if ($widgetObject) {
                $decodedData = json_decode($widget['data'], true);
                $newData = $widgetObject->duplicate($oldWidgetId, $newWidgetId, $decodedData);
            }
            self::updateWidget($newWidgetId, array('data' => $newData));

            $widgetIdTransition[$oldWidgetId] = $newWidgetId;
        }

        foreach ($widgetIdTransition as $oldId => $newId) {
            $sql = "
            UPDATE
                $widgetTable
            SET
                `blockName` = REPLACE(`blockName`, 'column" . (int)$oldId . "_', 'column" . (int)$newId . "_')
            WHERE
                `revisionId` = :newRevisionId
            ";
            ipDb()->execute($sql, array('newRevisionId' => $newRevisionId));
            ipEvent('ipWidgetDuplicated', array('oldWidgetId' => $oldId, 'newWidgetId' => $newId));
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

        self::$widgetObjects = ipFilter('ipWidgets', []);

        return self::$widgetObjects;
    }

    /**
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

    /**
     * @param int $widgetId
     * @return array|null
     */
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
     * @param int $pageId
     * @return array
     */
    public static function getRevisions($pageId)
    {
        return ipDb()->selectAll('revision', '*', array('pageId' => $pageId));
    }

    /**
     * Enter description here...
     *
     * @param string $widgetName
     * @param $data
     * @param string $skin
     * @param int $revisionId
     * @param int $languageId
     * @param string $blockName
     * @param int $position Real position of widget starting with 0
     * @param bool $visible
     * @return int
     */
    public static function createWidget(
        $widgetName,
        $data,
        $skin,
        $revisionId,
        $languageId,
        $blockName,
        $position,
        $visible = true
    ) {
        $positionNumber = self::_calcWidgetPositionNumber($revisionId, $languageId, null, $blockName, $position);

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
     * Return float number that will position widget in requested position.
     *
     * @param int $revisionId
     * @param int $languageId
     * @param int $widgetId
     * @param string $newBlockName
     * @param int $newPosition Real position of widget starting with 0
     * @return float
     */
    private static function _calcWidgetPositionNumber($revisionId, $languageId, $widgetId, $newBlockName, $newPosition)
    {
        $allWidgets = Model::getBlockWidgetRecords($newBlockName, $revisionId, $languageId);

        $widgets = [];

        foreach ($allWidgets as $widget) {
            if ($widgetId === null || $widget['id'] != $widgetId) {
                $widgets[] = $widget;
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

    /**
     * @param int $widgetId
     * @param array $data
     * @return int row count
     */
    public static function updateWidget($widgetId, $data)
    {
        if (array_key_exists('data', $data)) {
            $data['data'] = json_encode(\Ip\Internal\Text\Utf8::checkEncoding($data['data']));
        }

        return ipDb()->update('widget', $data, array('id' => $widgetId));
    }

    /**
     * @param int $revisionId
     */
    public static function removeRevisionWidgets($revisionId)
    {
        $widgets = ipDb()->selectColumn('widget', 'id', array('revisionId' => $revisionId));

        foreach ($widgets as $widgetId) {
            static::removeWidget($widgetId);
        }
    }

    /**
     * @param int $revisionId
     */
    public static function removeRevision($revisionId)
    {
        static::removeRevisionWidgets($revisionId);

        ipEvent('ipBeforeRevisionDelete', array('revisionId' => $revisionId));
        ipdb()->delete('revision', array('revisionId' => $revisionId));
    }

    /**
     * @param int $pageId
     */
    public static function removePageRevisions($pageId)
    {
        $revisions = self::getRevisions($pageId);
        foreach ($revisions as $revision) {
            self::removeRevision($revision['revisionId']);
        }
    }

    /**
     * Mark widget as deleted.
     *
     * @param int $widgetId
     */
    public static function deleteWidget($widgetId)
    {
        ipDb()->update('widget', array('deletedAt' => time(), 'isDeleted' => 1), array("id" => $widgetId));
    }

    /**
     * Completely remove widget.
     *
     * @param int $widgetId
     */
    public static function removeWidget($widgetId)
    {
        $widgetRecord = self::getWidgetRecord($widgetId);
        $widgetObject = self::getWidgetObject($widgetRecord['name']);

        ipEvent('ipBeforeWidgetRemoved', $widgetRecord);


        if ($widgetObject) {
            $widgetObject->delete($widgetId, $widgetRecord['data']);
        }

        ipDb()->delete('widget', array('id' => $widgetId));

        ipEvent('ipAfterWidgetRemoved', $widgetRecord);

    }

    /**
     * @param string $oldUrl
     * @param string $newUrl
     */
    public static function updateUrl($oldUrl, $newUrl)
    {
        $old = parse_url($oldUrl);
        $new = parse_url($newUrl);

        $oldPart = $old['host'] . $old['path'];
        $newPart = $new['host'] . $new['path'];

        $quotedPart = substr(ipDb()->getConnection()->quote('://' . $oldPart), 1, -1);

        $search = '%'. addslashes(substr(json_encode($quotedPart), 1, -1)) . '%';

        $table = ipTable('widget');

        $records = ipDb()->fetchAll("SELECT `id`, `data` FROM $table WHERE `data` LIKE ?", array($search));

        if (!$records) {
            return;
        }


        if ($newUrl == ipConfig()->baseUrl()) {
            //the website has been moved

            $search = '%\b(https?://)' . preg_quote($oldPart, '%') . '%';
        } else {
            //internal page url has changed

            // \b - start at word boundary
            // (https?://) - protocol
            // (/?) - allow optional slash at the end of url
            // (?= ) - symbols expected after url
            // \Z - end of subject or end of line
            $search = '%\b(https?://)' . preg_quote($oldPart, '%') . '(/?)(?=["\'?]|\s|\Z)%';
        }

        foreach ($records as $row) {
            $data = json_decode($row['data'], true);

            $data = self::replaceUrl($search, $newPart, $data);

            if (json_encode($data) != $row['data']) {
                ipDb()->update('widget', array('data' => json_encode($data)), array('id' => $row['id']));
            }
        }
    }

    /**
     * @param string $search
     * @param string $newPart
     * @param string|array $data
     * @return mixed
     */
    private static function replaceUrl($search, $newPart, $data){
        if (is_array($data)){
            foreach($data as &$val) {
                $val = self::replaceUrl($search, $newPart, $val);
            }
        } else {
            $data = preg_replace($search, '${1}' . $newPart . '${2}', $data);
        }
        return $data;
    }
    /**
     * @param int $revisionId
     * @return bool|string
     */
    public static function isRevisionModified($revisionId = null)
    {
        if ($revisionId === null) {
            $currentRevision = ipContent()->getCurrentRevision();
            $revisionId = $currentRevision['revisionId'];
        }

        $currentRevision = \Ip\Internal\Revision::getRevision($revisionId);
        if (!$currentRevision) {
            return false;
        }
        $pageId = $currentRevision['pageId'];

        $publishedRevision = \Ip\Internal\Revision::getPublishedRevision($pageId);
        if (!$publishedRevision) {
            return true;
        }

        if ($publishedRevision['revisionId'] == $currentRevision['revisionId']) {
            return false;
        }

        $currentFingerprint = self::revisionFingerprint($currentRevision['revisionId']);
        $publishedFingerprint = self::revisionFingerprint($publishedRevision['revisionId']);

        $modified = $currentFingerprint != $publishedFingerprint;

        return $modified;
    }

    /**
     * @param int $revisionId
     * @return string
     */
    protected static function revisionFingerprint($revisionId)
    {
        $table = ipTable('widget');
        // compare revision content
        $sql = "
            SELECT
                `name`
            FROM
                $table
            WHERE
              `revisionId` = :revisionId
              AND
              `name` != 'Columns'
              AND
              `isDeleted` = 0
            ORDER BY
              blockName, `position`
        ";

        $params = array(
            'revisionId' => $revisionId
        );

        $widgetNames = ipDb()->fetchColumn($sql, $params);

        // compare revision content
        $sql = "
            SELECT
                `data`
            FROM
                $table
            WHERE
              `revisionId` = :revisionId
              AND
              `name` != 'Columns'
              AND
              `isDeleted` = 0
            ORDER BY
              blockName, `position`
        ";

        $params = array(
            'revisionId' => $revisionId
        );

        $widgetData = ipDb()->fetchColumn($sql, $params);

        $fingerprint = implode('***|***', $widgetNames) . '|||' . implode('***|***', $widgetData);

        return $fingerprint;
    }

}
