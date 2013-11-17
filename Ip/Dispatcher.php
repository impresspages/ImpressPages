<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip;

/**
 *
 * Event dispatcher class
 *
 */
class Dispatcher{

    private $handlers;
    protected $initCompleted = false;


    public function __construct() {
        $this->handlers = array();
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

        if (! isset($this->handlers[$eventName])) {
            $this->handlers[$eventName] = array();
        }

        $this->handlers[$eventName][] = $callable;
    }

    /**
     *
     * Bind to a slot generation event
     * @param string $action Eg. module_name.event_name
     * @param callable $callable
     * @throws CoreException
     */
    public function bindSlot ($slot, $callable) {
        $this->bind('site.generateSlot.' . $slot, $callable);
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
        if ( ! isset($this->handlers[$event->getName()])) {
            return false;
        }

        foreach ($this->handlers[$event->getName()] as $callable) {
            call_user_func($callable, $event);
        }

        return $event->getProcessed();
    }


    public function notifyUntil(Event $event) {
        if ( ! isset($this->handlers[$event->getName()])) {
            return false;
        }

        foreach ($this->handlers[$event->getName()] as $callable) {
            call_user_func($callable, $event);
            if ($event->getProcessed() > 0){
                return $event->getProcessed();
            }
        }
    }

}