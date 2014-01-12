<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Form;




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
        self::$fields = ipFilter('ipWidgetFormFieldTypes', array());
    }
}