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
class Dispatcher{
    
    private $_handlers;
    
    
    public function __construct() {
        $this->_handlers = array();
    }

    /**
     * 
     * Bind action to callable function
     * @param string $action Eg. module_name.event_name
     * @param callable $callable
     * @throws Exception
     */
    public function bind ($eventName, $callable) {
        if (!is_callable($callable)) {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line']) {               
                $errorMessage = "Incorrect callable ".$callable." (Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ";
            } else {
                $errorMessage = "Incorrect callable ".$callable;
                throw new \Exception($errorMessage);
            }
        }
        
        if (! isset($this->_handlers[$eventName])) {
            $this->_handlers[$eventName] = array();
        }
        
        $this->_handlers[$eventName][] = $callable;                    
    }
    
    public function notify($event) {
        if ( ! isset($this->_handlers[$event->getName()])) {
            return false;
        }
        
        foreach ($this->_handlers[$event->getName()] as $callable) {
            call_user_func($callable, $event);
        }

        return $event->getProcessed();
    }
    
    
    public function notifyUntil($event) {
        if ( ! isset($this->_handlers[$event->getName()])) {
            return false;
        }
        
        foreach ($this->_handlers[$event->getName()] as $callable) {
            call_user_func($callable, $event);
            $event->addProcessed();
            if ($event->getProcessed() > 0){
                return $event->getProcessed();
            }
        }
    }    
    
}