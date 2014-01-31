<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip\Internal\Dispatcher;

/**
 * Filter dispatcher class
 *
 */
class FilterDispatcher extends EventListener
{
    /**
     * Trigger an event
     *
     * @param $eventName Event name
     * @param $value Data for filtering
     * @param array $data Data for event processing
     * @return null
     */
    public function filter($eventName, $value, $data = array())
    {
        if (!isset($this->listeners[$eventName])) {
            return $value;
        }

        if (!isset($this->sortedListeners[$eventName])) {
            ksort($this->listeners[$eventName]);
            $this->sortedListeners[$eventName] = true;
        }

        do {
            foreach (current($this->listeners[$eventName]) as $callable) {
                $value = call_user_func($callable, $value, $data);
            }
        } while (next($this->listeners[$eventName]) !== false);

        return $value;
    }
}
