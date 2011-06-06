<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Ip;


if (!defined('CMS')) exit;  

/**
 * 
 * Event dispatcher class
 * 
 */ 
class Event{
    
    private $object;
    private $eventName;
    private $values;

    public function __construct($object, $eventName, $values) {
        $this->object = $object;
        $this->eventName = $eventName;
        $this->values = $values;
    }
    
    public function getObject () {
        return $this->object;    
    }
    
    public function getEventName () {
        return $this->eventName;
    }
    
    
    public function getValues () {
        return $this->data;    
    }
    
    public function getValue ($valueKey) {
        if (isset($this->data[$valueKey])) {
            return $this->data[$valueKey];   
        }
        
        $trace = debug_backtrace();
        throw new Exception(
            'Undefined data variable via getData(): ' . $valueKey .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
    }
    
    public function valueExist($valueKey) {
        return isset($this->data[$valueKey]);
    }
    
}
