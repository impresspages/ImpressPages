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
    
    public static function generateWidgetManagement($widgetId) {
        $widgetRecord = self::getWidgetRecord($widgetId);
        
        $widgetData = json_decode($widgetRecord['data']);
        
        if (!is_array($widgetData)) {
            $widgetData = array();    
        }
        
        $widgetObject = self::getWidgetObject($widgetRecord['widgetName']);
        
        if (!$widgetObject) {
            throw new \Exception('Widget does not exist WidgetName: '.$widgetRecord['widgetName']);
        } 
        
        $managementHtml = $widgetObject->managementHtml($widgetId, $widgetData);
        
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
        		`created` = ".time().",
        		`modified` = ".time()."
        ";
        
        $rs = mysql_query($sql);
        
        if (!$rs) {
            throw new \Exception('Can\'t create new widget '.$sql.' '.mysql_error());
        }
        
        return mysql_insert_id();
    }
    
}