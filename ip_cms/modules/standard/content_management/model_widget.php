<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;

require_once(__DIR__.'/event_widget.php');

class ModelWidget{
    static private $widgets = null;
    
    public static function getWidgets() {
        global $dispatcher;
        
        if (self::$widgets !== null) {
            return self::$widgets;
        }
        
        $event = new EventWidget(null, 'contentManagement.collectWidgets', null);
        $dispatcher->notify($event);
        
        $widgets = $event->getWidgets();
        
        self::$widgets = $widgets;
        return self::$widgets;
    }
    
    public static function getWidget($widgetName) {
        global $dispatcher;
        
        $widgets = $this->getWidgets();
        
        if (isset($widgets[$name])) {
            return $widgets[$name];
        } else {
            return false;    
        }

    }
    
    public static function createWidget($widgetName, $priority, $blockName, $zoneName, $pageId, $layout) {
        $sql = "
        	insert into
        		".DB_PREF."m_standard_content_management_widget
        	set
        		`priority` = '".mysql_real_escape_string($priority)."',
        		`widgetName` = '".mysql_real_escape_string($widgetName)."',
        		`blockName` = '".mysql_real_escape_string($blockName)."',
        		`zoneName` = '".mysql_real_escape_string($zoneName)."',
        		`pageId` = '".mysql_real_escape_string($pageId)."',
        		`layout` = '".mysql_real_escape_string($layout)."',
        		`created` = ".time().",
        		`modified` = ".time()."
        ";
        
        $rs = mysql_query($sql);
        
        if (!$rs) {
            throw new Exception('Can\'t create new widget '.$sql.' '.mysql_error());
        }
        
        return mysql_insert_id();
    }
    
}