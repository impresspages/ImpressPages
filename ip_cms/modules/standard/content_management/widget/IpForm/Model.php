<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management\widget\IpForm;

if (!defined('CMS')) exit;



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
        global $dispatcher;
        //collect available field types
        $event = new \Modules\standard\content_management\EventFormFields(null, 'contentManagement.collectFieldTypes', null);
        $dispatcher->notify($event);
        self::$fields = $event->getFields();
    }
}