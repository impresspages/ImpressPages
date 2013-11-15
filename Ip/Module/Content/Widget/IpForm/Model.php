<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget\IpForm;




class Model{
    
    static $fields;
    
    public static function getAvailableFieldTypes() {
        if (!self::$fields) {
            self::collectFieldTypes();
        }
        
        return self::$fields;
    }
    
    public static function getFieldType($key) {
        if (!self::$fields) {
            self::collectFieldTypes();
        }
        if(isset(self::$fields[$key])) {
            return self::$fields[$key];
        }
        
        return false;
    }
    
    private static function collectFieldTypes() {
        //collect available field types
        $event = new \Ip\Module\Content\EventFormFields(null, 'contentManagement.collectFieldTypes', null);
        \Ip\ServiceLocator::getDispatcher()->notify($event);
        self::$fields = $event->getFields();
    }
}