<?php

namespace Modules\developer\widgets;


class Model {
    
    
    /**
     * 
     * Sort array of widgets
     * @param array $widgets
     */
    public static function sortWidgets($widgets) {
        $priorities = self::_getPriorities();
        $sortedWidgets = array();
        $unsortedWidgets = array();
        foreach ($widgets as $widgetKey => $widget) {
            if (isset($priorities[$widget->getName()])) {
                $position = $priorities[$widget->getName()];
                $sortedWidgets[(int)$position] = $widget;
            } else {
                $unsortedWidgets[] = $widget;
            }
        }
        ksort($sortedWidgets);
        $answer = array();
        foreach ($sortedWidgets as $widgetKey => $widget) {
            $answer[$widget->getName()] = $widget;
        }
        
        foreach ($unsortedWidgets as $widgetKey => $widget) {
            $answer[$widget->getName()] = $widget;
        }
        
        return $answer;
    }
    
    
    
    /**
     * 
     * Recreate list of widgets. If there are new widgets, add them to sorting table.
     * If some widgets don't exist anymore, remove them.
     * 
     * Algorithm:
     * 1. Mark all widgets as deleted
     * 2. Loop existing widgets and mark them as not deleted
     * 3. Add new widgets
     * 4. Remove widgets that still are marked as deleted
     * 
     */
    public static function recreateWidgetsList() {
        
        
        $sql = "
        UPDATE 
            `".DB_PREF."m_developer_widget_sort`
        SET
            `deleted` = 1
        WHERE
            1
        ";

        $rs = mysql_query($sql); 
        if (!$rs){
            throw new Exception('Can\'t mark widgets as deleted '.$sql.' '.mysql_error(), Exception::DB);
        }
        
        $widgets = \Ip\Module\Content\Model::getAvailableWidgetObjects();
        
        foreach ($widgets as $widgetKey => $widget) {
            self::_addWidget($widget->getName());
        }
        
        
        $sql = "
        DELETE FROM 
            `".DB_PREF."m_developer_widget_sort`
        WHERE
            `deleted`
        ";
        $rs = mysql_query($sql); 
        if (!$rs){
            throw new Exception('Can\'t remove deleted widgets '.$sql.' '.mysql_error(), Exception::DB);
        }
        
        
    }
    
    
    /**
     * 
     * Add new widget to sorting table. If widget already exists, ignore it.
     * @param string $widgetName
     */
    private static function _addWidget($widgetName) {
        $sql = "
        INSERT INTO 
            `".DB_PREF."m_developer_widget_sort`
        SET
            `widgetName` = '".mysql_real_escape_string($widgetName)."',
            `priority` = ".(int)self::_nextPriority()."
        ON DUPLICATE KEY UPDATE
            `deleted` = 0
        ";
        $rs = mysql_query($sql); 
        if (!$rs) {
            throw new Exception('Can\'t add widget '.$sql.' '.mysql_error(), Exception::DB);
        }
    }
    
    
    private static function _nextPriority() {
        $sql = "
        SELECT 
            max(`priority`) as 'maxPriority'
        FROM 
            `".DB_PREF."m_developer_widget_sort`
        WHERE
            1
        ";
        
        $rs = mysql_query($sql); 
        if (!$rs) {
            throw new Exception('Can\'t add widget '.$sql.' '.mysql_error(), Exception::DB);
        }
        
        if ($lock = mysql_fetch_assoc($rs)) {
            return $lock['maxPriority'] + 10;
        } else {
            return 10; //first element. Could be 0. But just tu have space at the top
        }
    }
    
    
    private static function _getPriorities() {
        $sql = "
        SELECT 
            *
        FROM
            `".DB_PREF."m_developer_widget_sort`
        WHERE
            1
        ORDER BY
            `priority` asc
        ";
        $rs = mysql_query($sql); 
        if (!$rs) {
            throw new Exception('Can\'t add widget '.$sql.' '.mysql_error(), Exception::DB);
        }
        
        $answer = array();
        
        while ($lock = mysql_fetch_assoc($rs)) {
            $answer[$lock['widgetName']] = $lock['priority'];
        }
        return $answer;
    }

}