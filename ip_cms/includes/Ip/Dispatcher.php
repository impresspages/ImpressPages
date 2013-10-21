<?php
/**
 * @package ImpressPages
 *
 *
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
    protected $initCompleted = false;


    public function __construct() {
        $this->_handlers = array();
        $this->bind('site.afterInit', array($this, 'registerInit'));
    }

    public function registerInit()
    {
        $this->initCompleted = true;
    }

    /**
     *
     * Bind action to callable function
     * @param string $action Eg. module_name.event_name
     * @param callable $callable
     * @throws CoreException
     */
    public function bind ($eventName, $callable) {
        if (!is_callable($callable)) {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                $errorMessage = "Incorrect callable ".$callable." (Error source: ".($backtrace[0]['file'])." line: ".($backtrace[0]['line'])." ) ";
            } else {
                $errorMessage = "Incorrect callable ".$callable;
            }
            throw new CoreException($errorMessage, CoreException::EVENT);
        }

        if (! isset($this->_handlers[$eventName])) {
            $this->_handlers[$eventName] = array();
        }

        $this->_handlers[$eventName][] = $callable;
    }

    public function notify(Event $event) {
        if (!$this->initCompleted && $event->getName() != 'site.afterInit') {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && isset($backtrace[0]['line'])) {
                $file = ' (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' )';
            } else {
                $file = '';
            }
            throw new \Ip\CoreException("Event notification can't be thrown before system init.".$file);
        }
        if ( ! isset($this->_handlers[$event->getName()])) {
            return false;
        }

        foreach ($this->_handlers[$event->getName()] as $callable) {
            call_user_func($callable, $event);
        }

        return $event->getProcessed();
    }


    public function notifyUntil(Event $event) {
        if ( ! isset($this->_handlers[$event->getName()])) {
            return false;
        }

        foreach ($this->_handlers[$event->getName()] as $callable) {
            call_user_func($callable, $event);
            if ($event->getProcessed() > 0){
                return $event->getProcessed();
            }
        }
    }

}