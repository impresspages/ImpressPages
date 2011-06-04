<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

 
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
    public function bind ($event, $callable) {
        if (!is_callable($callable)) {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])                
              $errorMessage = "Incorrect callable ".$callable." (Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ";
            else
              $errorMessage = "Incorrect callable ".$callable;
            throw new Exception($errorMessage);
        }
        
        if (! isset($this->_handlers[$event])) {
            $this->_handlers[$event] = array();
        }
        
        $this->_handlers[$event][] = $callable;                    
    }
    
    public function notify($object, $event, $data) {
        if ( ! isset($this->_handlers[$event])) {
            return;
        }
        
        foreach ($this->_handlers[$event] as $callable) {
            $callable($object, $event, $data);
        }
    }
    
    
}