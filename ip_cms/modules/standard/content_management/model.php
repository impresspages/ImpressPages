<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;

require_once(__DIR__.'/event_widget.php');

class Model{
    static private $widgetObjects = null;
    
    public static function generateBlock($blockName, $revisionId, $managementState) {
    	global $site;

    	$widgets = self::getBlockWidgetRecords($blockName, $revisionId);
    	
    	$widgetsHtml = array();
    	foreach ($widgets as $key => $widget) {
    		$widgetsHtml[] = self::_generateWidgetPreview($widget, $managementState);
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
    


    public static function generateWidgetPreview($instanceId, $managementState) {
        $widgetRecord = self::getWidgetFullRecord($instanceId);
        return self::_generateWidgetPreview($widgetRecord, $managementState);
    }
    
    private static function _generateWidgetPreview($widgetRecord, $managementState) {        
        $widgetData = $widgetRecord['data'];
        if (!is_array($widgetData)) {
            $widgetData = array();    
        }
        
        $widgetObject = self::getWidgetObject($widgetRecord['name']);
        
        if (!$widgetObject) {
            throw new \Exception('Widget does not exist. Widget name: '.$widgetRecord['name']);
        } 
        
        $previewHtml = $widgetObject->previewHtml($widgetRecord['widgetId'], $widgetData);
        
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
            throw new \Exception('Widget does not exist. Widget name: '.$widgetRecord['name']);
        } 
        
        $managementHtml = $widgetObject->managementHtml($widgetRecord['widgetId'], $widgetData);
        
        $data = array (
            'managementHtml' => $managementHtml,
            'widgetRecord' => $widgetRecord
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
            throw new \Exception('Can\'t get widgets '.$sql.' '.mysql_error());
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
            throw new \Exception('Can\'t get revision data '.$sql.' '.mysql_error());
        }        
        
        while ($lock = mysql_fetch_assoc($rs)) {
            
            $dataSql = '';
            
            foreach ($lock as $key => $value) {
                if ($key != 'revisionId' && $key != 'instanceId' ) {
                    if ($dataSql != '') {
                        $dataSql .= ', ';    
                    }
                    $dataSql .= " `".$key."` = '".mysql_real_escape_string($value)."' ";
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
                throw new \Exception('Can\'t get revision data '.$insertSql.' '.mysql_error());
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
            throw new \Exception('Can\'t find widget '.$sql.' '.mysql_error());
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
     * @throws \Exception
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
            throw new \Exception('Can\'t find widget '.$sql.' '.mysql_error());
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
                `position` < ".$record['position']."  
        ";    
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t find widget '.$sql.' '.mysql_error());
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
            if ($newPosition == 0) {
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
     * @throws \Exception
     */
    public static function createWidget($widgetName, $data, $layout, $predecessor) {
        $sql = "
          insert into
              ".DB_PREF."m_content_management_widget
          set
              `name` = '".mysql_real_escape_string($widgetName)."',
              `layout` = '".mysql_real_escape_string($layout)."',
              `created` = ".time().",
              `data` = '".mysql_real_escape_string(json_encode($data))."'
        ";
        
        $rs = mysql_query($sql);
        
        if (!$rs) {
            throw new \Exception('Can\'t create new widget '.$sql.' '.mysql_error());
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
                $dataSql .= " `".$key."` = '".mysql_real_escape_string(json_encode($data))."' ";
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
            throw new \Exception('Can\'t update widget '.$sql.' '.mysql_error());
        }
        
        return true; 
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
            throw new \Exception('Can\'t create instance '.$sql.' '.mysql_error());
        }
        
        return mysql_insert_id(); 
    }    
    
    

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
            throw new \Exception('Can\'t delete instance '.$sql.' '.mysql_error());
        }
        
        return true; 
    }
        
    
    public static function moveInstance($instanceId, $newRevisionId, $newBlockName, $newPosition) {
        throw new \Exception('refactoring needed');
        
        $record = Model::getWidgetFullRecord($instanceId);
        
        Model::deleteInstance($instanceId);
        $newInstanceId = Model::addInstance($record['widgetId'], $newRevisionId, $newBlockName, $newPosition, $record['visible']);

        return $newInstanceId;         
    }
    
}