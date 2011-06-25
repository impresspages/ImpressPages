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
    
    public static function generateBlock($blockName, $revision) {
    	global $site;

    	$widgets = self::getRevisionWidgetRecords($revision['id']);
    	
    	$widgetsHtml = array();
    	foreach ($widgets as $key => $widget) {
    		$widgetsHtml[] = self::generateWidgetPreview($widget);
    	}

    	$data = array (
    		'widgetsHtml' => $widgetsHtml,
    		'blockName' => $blockName,    		
    		'revision' => $revision
    	);
    	
    	$answer = \Ip\View::create('standard/content_management/view/block.php', $data)->render();
    	return $answer;
    }
    
    public static function getRevisionWidgetRecords($revisionId){
        $sql = "
        	SELECT w.*, rtw.revisionId, rtw.blockName 
        	FROM
        		`".DB_PREF."m_content_management_revision_to_widget` rtw,
        		`".DB_PREF."m_content_management_widget` w
        	WHERE
        		rtw.widgetId = w.id
     		ORDER BY `position` ASC
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t get widgets '.$sql.' '.mysql_error());
        }
        
        $answer = array();
        
        while ($lock = mysql_fetch_assoc($rs)) {
            $answer[] = $lock;
        }
            	
    	return $answer;
    }
    
    public static function getLastRevision($zoneName, $pageId) {
        $sql = "
        	SELECT * FROM `".DB_PREF."m_content_management_revision`
        	WHERE
        		`zoneName` = '".mysql_real_escape_string($zoneName)."' AND
        		`pageId` = '".(int)$pageId."'
     		ORDER BY `created` DESC
     		LIMIT 1
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t find last revision '.$sql.' '.mysql_error());
        }
        
        if ($lock = mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            return false;
        }    	
    	
    }
    
    public static function getRevision($revisionId) {
        $sql = "
        	SELECT * FROM `".DB_PREF."m_content_management_revision`
        	WHERE `id` = ".(int)$revisionId."
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t find revision '.$sql.' '.mysql_error());
        }
        
        if ($lock = mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            return false;
        }    	
    	
    }
        
    
    public static function createRevision ($zoneName, $pageId) {
        $sql = "
        	INSERT INTO `".DB_PREF."m_content_management_revision`
        	SET
        		`zoneName` = '".mysql_real_escape_string($zoneName)."',
        		`pageId` = '".(int)$pageId."',
        		`published` = 0,
        		`created` = ".time()."
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t create new revision '.$sql.' '.mysql_error());
        }
        
        return mysql_insert_id();        
    }
    
    public static function duplicateRevision($revisionId) {
    	
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
        	WHERE `id` = ".(int)$widgetId."
        ";    
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new \Exception('Can\'t find widget '.$sql.' '.mysql_error());
        }
        
        if ($lock = mysql_fetch_assoc($rs)) {
            return $lock;
        } else {
            return false;
        }
    }

    public static function generateWidgetPreview($widgetId) {
        $widgetRecord = self::getWidgetRecord($widgetId);
        return self::_generateWidgetPreview($widgetRecord);
    }
    
    private static function _generateWidgetPreview($widgetRecord) {
        $widgetData = json_decode($widgetRecord['data']);
        
        if (!is_array($widgetData)) {
            $widgetData = array();    
        }
        
        $widgetObject = self::getWidgetObject($widgetRecord['widgetName']);
        
        if (!$widgetObject) {
            throw new \Exception('Widget does not exist WidgetName: '.$widgetRecord['widgetName']);
        } 
        
        $managementHtml = $widgetObject->previewHtml($widgetRecord['id'], $widgetData);
        
        $data = array (
            'managementHtml' => $managementHtml,
            'widgetName' => $widgetRecord['widgetName']
        );
        $answer = \Ip\View::create('standard/content_management/view/widget_preview.php', $data)->render();
        return $answer;    
    }
    
    public static function generateWidgetManagement($widgetId) {
        $widgetRecord = self::getWidgetRecord($widgetId);
        return self::_generateWidgetManagement($widgetRecord);
    }
    
    private static function _generateWidgetManagement($widgetRecord) {
        $widgetData = json_decode($widgetRecord['data']);
        
        if (!is_array($widgetData)) {
            $widgetData = array();    
        }
        
        $widgetObject = self::getWidgetObject($widgetRecord['widgetName']);
        
        if (!$widgetObject) {
            throw new \Exception('Widget does not exist WidgetName: '.$widgetRecord['widgetName']);
        } 
        
        $managementHtml = $widgetObject->managementHtml($widgetRecord['id'], $widgetData);
        
        $data = array (
            'managementHtml' => $managementHtml,
            'widgetName' => $widgetRecord['widgetName']
        );
        $answer = \Ip\View::create('standard/content_management/view/widget_management.php', $data)->render();
        return $answer;    
    }
    
    public static function createWidget($position, $zoneName, $blockName, $pageId, $widgetName, $layout) {
        $sql = "
        	insert into
        		".DB_PREF."m_content_management_widget
        	set
        		`position` = '".mysql_real_escape_string($position)."',
        		`zoneName` = '".mysql_real_escape_string($zoneName)."',
        		`blockName` = '".mysql_real_escape_string($blockName)."',
        		`pageId` = '".mysql_real_escape_string($pageId)."',
        		`visible` = 1,
        		`widgetName` = '".mysql_real_escape_string($widgetName)."',
        		`layout` = '".mysql_real_escape_string($layout)."',
        		`created` = ".time()."
        ";
        
        $rs = mysql_query($sql);
        
        if (!$rs) {
            throw new \Exception('Can\'t create new widget '.$sql.' '.mysql_error());
        }
        
        return mysql_insert_id();
    }
    
}