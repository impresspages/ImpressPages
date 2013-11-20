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
class Dispatcher
{

    protected $handlers;

    /**
     * @var array stores info which handlers are sorted
     */
    protected $sortedHandlers;
    protected $initCompleted = false;


    public function __construct()
    {
        $this->handlers = array();
        $this->sortedHandlers = array();
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
    public function bind($eventName, $callable, $priority = 10)
    {
        if (!is_callable($callable)) {
            $backtrace = debug_backtrace();
            if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                $errorMessage = "Incorrect callable " . $callable . " (Error source: " . ($backtrace[0]['file']) . " line: " . ($backtrace[0]['line']) . " ) ";
            } else {
                $errorMessage = "Incorrect callable " . $callable;
            }
            throw new CoreException($errorMessage, CoreException::EVENT);
        }

        if (!isset($this->handlers[$eventName][$priority])) {
            $this->handlers[$eventName][$priority] = array();
        }

        $this->handlers[$eventName][$priority][] = $callable;
        unset($this->sortedHandlers[$eventName]);
    }

    /**
     *
     * Bind to a slot generation event
     * @param string $action Eg. module_name.event_name
     * @param callable $callable
     * @throws CoreException
     */
    public function bindSlot($slot, $callable, $priority = 10)
    {
        $this->bind('site.generateSlot.' . $slot, $callable, $priority);
    }

    private function check($eventName)
    {
        if (!$this->initCompleted && $eventName != 'site.afterInit') {
            $backtrace = debug_backtrace();
            if (isset($backtrace[1]['file']) && isset($backtrace[1]['line'])) {
                $file = ' (Error source: ' . $backtrace[1]['file'] . ' line: ' . $backtrace[1]['line'] . ' )';
            } else {
                $file = '';
            }
            throw new \Ip\CoreException("Event notification can't be thrown before system init." . $file);
        }

    }

    public function filter($eventName, $value, $data = array())
    {
        $this->check($eventName);

        if (!isset($this->handlers[$eventName])) {
            return $value;
        }

        if (isset($this->sortedHandlers[$eventName])) {
            ksort($this->handlers[$eventName]);
            $this->sortedHandlers[$eventName] = true;
        }

        do {
            foreach (current($this->handlers[$eventName]) as $callable) {
                $value = call_user_func($callable, $value, $data);
            }
        } while (next($this->handlers[$eventName]) !== false);

        return $value;
    }

    public function job($eventName, $data = array())
    {
        $this->check($eventName);

        if (!isset($this->handlers[$eventName])) {
            return null;
        }

        if (isset($this->sortedHandlers[$eventName])) {
            ksort($this->handlers[$eventName]);
            $this->sortedHandlers[$eventName] = true;
        }

        do {
            foreach (current($this->handlers[$eventName]) as $callable) {
                $result = call_user_func($callable, $data);
                if ($result !== null) {
                    return $result;
                }
            }
        } while (next($this->handlers[$eventName]) !== false);

        return null;
    }

    public function notify($eventName, $data = array())
    {
        $this->check($eventName);

        if (!isset($this->handlers[$eventName])) {
            return null;
        }

        if (isset($this->sortedHandlers[$eventName])) {
            ksort($this->handlers[$eventName]);
            $this->sortedHandlers[$eventName] = true;
        }

        do {
            foreach (current($this->handlers[$eventName]) as $callable) {
                call_user_func($callable, $data);
            }
        } while (next($this->handlers[$eventName]) !== false);
    }

}