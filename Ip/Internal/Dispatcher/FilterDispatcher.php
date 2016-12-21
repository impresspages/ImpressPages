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
     * @param string $eventName Event name
     * @param mixed $value Data for filtering
     * @param array $data Data for event processing
     * @return null
     */
    public function filter($eventName, $value, $data = [])
    {
        if (empty($this->listeners[$eventName])) {
            return $value;
        }

        if (!isset($this->sortedListeners[$eventName])) {
            ksort($this->listeners[$eventName]);
            $this->sortedListeners[$eventName] = true;
        }

        reset($this->listeners[$eventName]);
        do {
            foreach (current($this->listeners[$eventName]) as $callable) {
                $value = call_user_func($callable, $value, $data);
            }
        } while (next($this->listeners[$eventName]) !== false);

        return $value;
    }
}
