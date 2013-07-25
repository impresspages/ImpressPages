<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;

require_once(__DIR__.'/event_widget.php');
require_once(__DIR__.'/exception.php');
require_once (BASE_DIR.INCLUDE_DIR.'db_system.php');


class Model{
    static private $widgetObjects = null;
    const DEFAULT_LAYOUT = 'default';
    const WIDGET_DIR = 'widget';

    public static function generateBlock($blockName, $revisionId, $managementState) {
        global $site;
        $widgets = self::getBlockWidgetRecords($blockName, $revisionId);
        $widgetsHtml = array();
        foreach ($widgets as $key => $widget) {
            try {
                $widgetsHtml[] = self::_generateWidgetPreview($widget, $managementState);
            } catch (Exception $e) {
                if ($e->getCode() == Exception::UNKNOWN_WIDGET) {
                    $viewData = array (
                   'widgetRecord' => $widget,
                   'managementState' => $managementState
                    );
                    $widgetsHtml[] = \Ip\View::create('view/unknown_widget.php', $viewData)->render();
                } else {
                    throw new Exception('Error when generating widget preview', null, $e);
                }
            }
        }

        $data = array (
            'widgetsHtml' => $widgetsHtml,
            'blockName' => $blockName,    		
            'revisionId' => $revisionId,
            'managementState' => $managementState
        );
        $answer = \Ip\View::create('view/block.php', $data)->render();
        return $answer;
    }

    public static function generateWidgetPreviewFromStaticData($widgetName, $data, $layout = null) {
        if ($layout == null) {
            $layout = self::DEFAULT_LAYOUT;
        }
        $widgetObject = self::getWidgetObject($widgetName);
        if (!$widgetObject) {
            throw new Exception('Widget ' . $widgetName . ' does not exist', Exception::UNKNOWN_WIDGET);
        }
        
        $previewHtml = $widgetObject->previewHtml(null, $data, $layout);
        
        $widgetRecord = array (
            'widgetId' => null,
            'name' => $widgetName,
            'layout' => $layout,
            'data' => $data,
            'created' => time(),
            'recreated' => time(),
            'predecessor' => null,
        
            'instanceId' => null,
            'revisionId' => null,
            'position' => null,
            'blockName' => null,
            'visible' => 1,
            'created' => time(),
            'deleted' => null
        );
        return self::_generateWidgetPreview($widgetRecord, FALSE);
        /*
        $data = array (
            'html' => $previewHtml,
            'widgetRecord' => $data, //static data used instead of widget record from the database
            'managementState' => FALSE
        );
        $answer = \Ip\View::create('view/widget_preview.php', $data)->render();
        return $answer;*/
    }


    public static function generateWidgetPreview($instanceId, $managementState) {
        $widgetRecord = self::getWidgetFullRecord($instanceId);
        return self::_generateWidgetPreview($widgetRecord, $managementState);
    }

    private static function _generateWidgetPreview($widgetRecord, $managementState) {
        //check if we don't need to recreate the widget
        $themeChanged = \DbSystem::getSystemVariable('theme_changed');
        if ($themeChanged > $widgetRecord['recreated']) {
            $widgetData = $widgetRecord['data'];
            if (!is_array($widgetData)) {
                $widgetData = array();
            }
            $widgetObject = self::getWidgetObject($widgetRecord['name']);
            $newData = $widgetObject->recreate($widgetRecord['instanceId'], $widgetData);
            self::updateWidget($widgetRecord['widgetId'], array('recreated' => time(), 'data' =>  $newData));
            $widgetRecord = self::getWidgetFullRecord($widgetRecord['instanceId']);
        }
        
        
        
        $widgetData = $widgetRecord['data'];
        if (!is_array($widgetData)) {
            $widgetData = array();
        }

        
        $widgetObject = self::getWidgetObject($widgetRecord['name']);
        
        if (!$widgetObject) {
            throw new Exception('Widget does not exist. Widget name: '.$widgetRecord['name'], Exception::UNKNOWN_WIDGET);
        }

        $previewHtml = $widgetObject->previewHtml($widgetRecord['instanceId'], $widgetData, $widgetRecord['layout']);

        if ($managementState) {
            $previewHtml = preg_replace("/".str_replace(array('/', ':'), array('\\/', '\\:'), BASE_URL)."([^\\\"\\'\>\<\?]*)?\?([^\\\"]*)(?=\\\")/", '$0&cms_action=manage', $previewHtml);
            $previewHtml = preg_replace("/".str_replace(array('/', ':'), array('\\/', '\\:'), BASE_URL)."([^\\\"\\'\>\<\?]*)?(?=\\\")/", '$0?cms_action=manage', $previewHtml);
        }
        
        $data = array (
            'html' => $previewHtml,
            'widgetRecord' => $widgetRecord,
            'managementState' => $managementState
        );
        $answer = \Ip\View::create('view/widget_preview.php', $data)->render();
        return $answer;
    }

    public static function generateWidgetManagement($instanceId) {
        $widgetRecord = self::getWidgetFullRecord($instanceId);
        return self::_generateWidgetManagement($widgetRecord);
    }

    private static function _generateWidgetManagement($widgetRecord) {
        $widgetData = $widgetRecord['data'];

        if (!is_array($widgetData)) {
            $widgetData = array();
        }

        $widgetObject = self::getWidgetObject($widgetRecord['name']);

        if (!$widgetObject) {
            throw new Exception('Widget does not exist. Widget name: '.$widgetRecord['name'], Exception::DB);
        }

        $managementHtml = $widgetObject->managementHtml($widgetRecord['instanceId'], $widgetData, $widgetRecord['layout']);
        $widgetRecord['data'] = $widgetObject->dataForJs($widgetRecord['data']); 
        $data = array (
            'managementHtml' => $managementHtml,
            'widgetRecord' => $widgetRecord,
            'layouts' => $widgetObject->getLayouts(),
            'widgetTitle' => $widgetObject->getTitle()
        );
        $answer = \Ip\View::create('view/widget_management.php', $data)->render();

        return $answer;
    }


    public static function getBlockWidgetRecords($blockName, $revisionId){
        $sql = "
            SELECT * 
            FROM
                `".DB_PREF."m_content_management_widget_instance` i,
                `".DB_PREF."m_content_management_widget` w
            WHERE
                i.deleted is NULL AND
                i.widgetId = w.widgetId AND
                i.blockName = '".mysql_real_escape_string($blockName)."' AND
                i.revisionId = ".(int)$revisionId."
            ORDER BY `position` ASC
        ";
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t get widgets '.$sql.' '.mysql_error(), Exception::DB);
        }

        $answer = array();

        while ($lock = mysql_fetch_assoc($rs)) {
            $lock['data'] = json_decode($lock['data'], true);
            $answer[] = $lock;
        }

        return $answer;
    }




    public static function duplicateRevision($oldRevisionId, $newRevisionId) {
        $sql = "
            SELECT * 
            FROM
                `".DB_PREF."m_content_management_widget_instance` i
            WHERE
                i.revisionId = ".(int)$oldRevisionId." AND
                i.deleted IS NULL
            ORDER BY `position` ASC
        ";    

        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t get revision data '.$sql.' '.mysql_error(), Exception::DB);
        }

        while ($lock = mysql_fetch_assoc($rs)) {

            $dataSql = '';

            foreach ($lock as $key => $value) {
                if ($key != 'revisionId' && $key != 'instanceId' ) {
                    if ($dataSql != '') {
                        $dataSql .= ', ';
                    }
                    if ($value !== null) {
                        $dataSql .= " `".$key."` = '".mysql_real_escape_string($value)."' ";
                    } else {
                        $dataSql .= " `".$key."` = NULL ";
                    }

                }
            }

            $insertSql = "
                INSERT INTO
                    `".DB_PREF."m_content_management_widget_instance`
                SET
                    ".$dataSql.",
                    `revisionId` = ".(int)$newRevisionId."                     
                    
            ";    

            $insertRs = mysql_query($insertSql);
            if (!$insertRs){
                throw new Exception('Can\'t get revision data '.$insertSql.' '.mysql_error(), Exception::DB);
            }
        }

    }

    public static function getAvailableWidgetObjects() {
        global $dispatcher;

        if (self::$widgetObjects !== null) {
            return self::$widgetObjects;
        }

        $event = new EventWidget(null, 'contentManagement.collectWidgets', null);
        $dispatcher->notify($event);

        $widgetObjects = $event->getWidgets();

        self::$widgetObjects = $widgetObjects;
        return self::$widgetObjects;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $widgetName
     * @return \Modules\standard\content_management\Widget
     */
    public static function getWidgetObject($widgetName) {
        global $dispatcher;

        $widgetObjects = self::getAvailableWidgetObjects();

        if (isset($widgetObjects[$widgetName])) {
            return $widgetObjects[$widgetName];
        } else {
            return false;
        }

    }

    public static function getWidgetRecord($widgetId) {
        $sql = "
            SELECT * FROM `".DB_PREF."m_content_management_widget`
            WHERE `widgetId` = ".(int)$widgetId."
        ";    

        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t find widget '.$sql.' '.mysql_error(), Exception::DB);
        }

        if ($lock = mysql_fetch_assoc($rs)) {
            $lock['data'] = json_decode($lock['data'], true);
            return $lock;
        } else {
            return false;
        }
    }


    /**
     *
     * getWidgetFullRecord differ from getWidgetRecord by including the information from m_content_management_widget_instance table.
     * @param int $instanceId
     * @throws Exception
     */
    public static function getWidgetFullRecord($instanceId) {
        $sql = "
            SELECT * FROM
                `".DB_PREF."m_content_management_widget_instance` i,
                `".DB_PREF."m_content_management_widget` w
            WHERE
                i.`instanceId` = ".(int)$instanceId." AND
                i.widgetId = w.widgetId 
        ";    
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t find widget '.$sql.' '.mysql_error(), Exception::DB);
        }

        if ($lock = mysql_fetch_assoc($rs)) {
            $lock['data'] = json_decode($lock['data'], true);
            return $lock;
        } else {
            return false;
        }
    }
    
    
    public static function getRevisions($zoneName, $pageId) {
        $sql = "
            SELECT * FROM
                `".DB_PREF."revision` 
            WHERE
                `zoneName` = '".mysql_real_escape_string($zoneName)."'
                AND
                `pageId` = ".(int)$pageId."
        ";
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t get revisions '.$sql.' '.mysql_error(), Exception::DB);
        }
        
        $answer = array();

        while ($lock = mysql_fetch_assoc($rs)) {
            $answer[] = $lock;
        }

        return $answer;
    }
    

    
    public static function updatePageRevisionsZone($pageId, $oldZoneName, $newZoneName) {
        $sql = "
            UPDATE
                `".DB_PREF."revision`
            SET
                 `zoneName` = '".mysql_real_escape_string($newZoneName)."'
            WHERE
                `zoneName` = '".mysql_real_escape_string($oldZoneName)."'
                AND
                `pageId` = ".(int)$pageId."
        ";
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t udpate revisions '.$sql.' '.mysql_error(), Exception::DB);
        }
        return mysql_affected_rows();
    }
    

    /**
     *
     * Find position of widget in current block
     * @param int $instanceId
     * @return int position of widget or null if widget does not exist
     */
    public static function getInstancePosition($instanceId) {
        $record = Model::getWidgetFullRecord($instanceId);

        $sql = "
            SELECT count(instanceId) as position FROM
                `".DB_PREF."m_content_management_widget_instance` 
            WHERE
                `revisionId` = ".$record['revisionId']." AND
                `blockName` = '".mysql_real_escape_string($record['blockName'])."' AND
                `position` < ".$record['position']." AND
                `deleted` IS NULL  
        ";    
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t find widget '.$sql.' '.mysql_error(), Exception::DB);
        }

        if ($lock = mysql_fetch_assoc($rs)) {
            return $lock['position'];
        } else {
            return false;
        }

    }

    /**
     *
     * Return float number that will position widget in requested position
     * @param int $instnaceId
     * @param string $blockName
     * @param int $newPosition Real position of widget starting with 0
     */
    private static function _calcWidgetPositionNumber($revisionId, $instanceId, $newBlockName, $newPosition) {
        $allWidgets = self::getBlockWidgetRecords($newBlockName, $revisionId);

        $widgets = array();

        foreach ($allWidgets as $widgetKey => $instance) {
            if ($instanceId === null || $instance['instanaceId'] != $instanceId) {
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
    public static function createWidget($widgetName, $data, $layout, $predecessor) {
        if ($layout == null) {
            $layout = self::DEFAULT_LAYOUT;
        }
        
        if ($predecessor === null) {
            $predecessorSql = ' NULL ';
        } else {
            $predecessorSql = (int)$predecessor;
        }
        
        $sql = "
          insert into
              ".DB_PREF."m_content_management_widget
          set
              `name` = '".mysql_real_escape_string($widgetName)."',
              `layout` = '".mysql_real_escape_string($layout)."',
              `created` = ".time().",
              `recreated` = ".time().",
              `data` = '".mysql_real_escape_string(json_encode(\Library\Php\Text\Utf8::checkEncoding($data)))."',
              `predecessor` = ".$predecessorSql."
              ";

        $rs = mysql_query($sql);

        if (!$rs) {
            throw new Exception('Can\'t create new widget '.$sql.' '.mysql_error(), Exception::DB);
        }

        $widgetId = mysql_insert_id();

        return $widgetId;

    }


    public static function updateWidget($widgetId, $data) {

        $dataSql = '';

        foreach ($data as $key => $value) {
            if ($dataSql != '') {
                $dataSql .= ', ';
            }

            if ($key == 'data') {
                $dataSql .= " `".$key."` = '".mysql_real_escape_string(json_encode(\Library\Php\Text\Utf8::checkEncoding($value)))."' ";
            } else {
                $dataSql .= " `".$key."` = '".mysql_real_escape_string($value)."' ";
            }
        }


        $sql = "
            UPDATE `".DB_PREF."m_content_management_widget`
            SET
                ".$dataSql."
            WHERE `widgetId` = ".(int)$widgetId."
        ";    

        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t update widget '.$sql.' '.mysql_error());
        }

        return true;
    }

    public static function updateInstance($instanceId, $data) {

        $dataSql = '';

        foreach ($data as $key => $value) {
            if ($dataSql != '') {
                $dataSql .= ', ';
            }
            $dataSql .= " `".$key."` = '".mysql_real_escape_string($value)."' ";
        }


        $sql = "
            UPDATE `".DB_PREF."m_content_management_widget_instance`
            SET
                ".$dataSql."
            WHERE `instanceId` = ".(int)$widgetId."
        ";    

        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t update instance '.$sql.' '.mysql_error(), Exception::DB);
        }

        return true;
    }

    /**
     * Returns possible layout pages.
     * blank.php and files starting with underscore (for example, _layout.php) are considered hidden.
     *
     * @param string $theme
     * @param bool $includeHidden true - returns all layouts, false - only public layouts
     * @return array layouts (e.g. ['main.php', 'blank.php'])
     */
    public static function getThemeLayouts($theme = THEME, $includeHidden = false) {
        $themeDir = BASE_DIR . THEME_DIR . $theme;

        $files = scandir($themeDir);
        $layouts = array();

        foreach ($files as $filename) {
            if ('php' == strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
                if ($includeHidden) {
                    $layouts[]= $filename;
                } elseif ($filename != 'blank.php' && $filename[0] != '_') {
                    $layouts[]= $filename;
                }
            }
        }

        return $layouts;
    }

    public static function addInstance($widgetId, $revisionId, $blockName, $position, $visible) {

        $positionNumber = Model::_calcWidgetPositionNumber($revisionId, null, $blockName, $position);

        $sql = "
            INSERT INTO `".DB_PREF."m_content_management_widget_instance`
            SET
                `widgetId` = ".(int)$widgetId.",
                `revisionId` = ".(int)$revisionId.",
                `blockName` = '".mysql_real_escape_string($blockName)."',
                `position` = '".$positionNumber."', 
                `visible` = ".(int)$visible.",
                `created` = ".(int)time().",
                `deleted` = NULL 
                
        ";    

        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t create instance '.$sql.' '.mysql_error(), Exception::DB);
        }

        return mysql_insert_id();
    }



    /**
     * 
     * Mark instance as deleted. Instance will be remove completely, when revision will be deleted.
     * @param int $instanceId
     */
    public static function deleteInstance($instanceId) {
        $sql = "
            UPDATE `".DB_PREF."m_content_management_widget_instance`
            SET
                `deleted` = ".(int)time()."
            WHERE
                `instanceId` = ".(int)$instanceId."
        ";    

        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t delete instance '.$sql.' '.mysql_error(), Exception::DB);
        }

        return true;
    }
    
    public static function removeRevision($revisionId) {
        $sql = "
            DELETE FROM
                `".DB_PREF."m_content_management_widget_instance` 
            WHERE
                `revisionId` = ".(int)$revisionId."
        ";
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t remove revision widgets instances '.$sql.' '.mysql_error(), Exception::DB);
        }
        
        $sql = "
            DELETE FROM
                `".DB_PREF."revision` 
            WHERE
                `revisionId` = ".(int)$revisionId."
        ";
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t remove revision '.$sql.' '.mysql_error(), Exception::DB);
        }        
    }

    
    public static function removePageRevisions($zoneName, $pageId) {
        $revisions = self::getRevisions($zoneName, $pageId);
        foreach($revisions as $revisionKey => $revision) {
            self::removeRevision($revision['revisionId']);
        } 
        
        self::deleteUnusedWidgets();
    }
    
    

    /**
     * 
     * Each widget might be used many times. That is controlled using instanaces. This method destroys all widgets that has no instances.
     * @throws Exception
     */
    public static function deleteUnusedWidgets() {
    

        $sql = "
            SELECT
                w.widgetId 
            FROM
                `".DB_PREF."m_content_management_widget` w
            LEFT JOIN
                `".DB_PREF."m_content_management_widget_instance` i
            ON
                i.widgetId = w.widgetId
            WHERE
                i.instanceId IS NULL
          
        ";
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t get unused widgets '.$sql.' '.mysql_error(), Exception::DB);
        }
        while ($lock = mysql_fetch_assoc($rs)) {
            self::deleteWidget($lock['widgetId']);
        }
    }
    
    /**
     * 
     * Completely remove widget.
     * @param int $widgetId
     */
    public static function deleteWidget($widgetId){
        $widgetRecord = self::getWidgetRecord($widgetId);
        $widgetObject = self::getWidgetObject($widgetRecord['name']);
        
        if ($widgetObject) {
            $widgetObject->delete($widgetId, $widgetRecord['data']);
        }
        
        $sql = "
          DELETE FROM
              `".DB_PREF."m_content_management_widget`
          WHERE
              `widgetId` = ".(int)$widgetId."
        ";
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t delete widget '.$sql.' '.mysql_error(), Exception::DB);
        }
    }

    
    public static function clearCache($revisionId) {
        require_once (BASE_DIR.LIBRARY_DIR.'php/text/html2text.php');
        
        $revision = \Ip\Revision::getRevision($revisionId);
        $pageContent = Model::generateBlock('main', $revisionId, FALSE);
        
        $html2text = new \Library\Php\Text\Html2Text();
        $html2text->set_html($pageContent);
        $pageContentText = $html2text->get_text();
        
        $params = array (
            'cached_html' => $pageContent,
            'cached_text' => $pageContentText
        );
        \Modules\standard\menu_management\Db::updatePage($revision['zoneName'], $revision['pageId'], $params);
    }




}