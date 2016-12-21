<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip\Internal\Dispatcher;

class EventListener
{

    protected $listeners = [];

    /**
     * @var array stores info which handlers are sorted
     */
    protected $sortedListeners = [];

    protected function callableError($callable, $backtrace)
    {
        if (isset($backtrace[1]['file']) && $backtrace[1]['line']) {
            $errorMessage = "Incorrect callable " . $callable . " (Error source: " . ($backtrace[1]['file']) . " line: " . ($backtrace[1]['line']) . " ) ";
        } else {
            $errorMessage = "Incorrect callable " . $callable;
        }

        throw new \Ip\Exception\Dispatcher($errorMessage);
    }

    /**
     * Register event listener
     *
     * @param string $name Event name
     * @param string $callable Callable method name. This method is called on specified event.
     * @param int $priority Event priority. Lower number means higher priority.
     */
    public function addListener($name, $callable, $priority = 50)
    {
        if (!is_callable($callable)) {
            $this->callableError($callable, debug_backtrace());
        }

        if (!isset($this->listeners[$name][$priority])) {
            $this->listeners[$name][$priority] = [];
        }

        $this->listeners[$name][$priority][] = $callable;
        unset($this->sortedListeners[$name]);
    }
}
