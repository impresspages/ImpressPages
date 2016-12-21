<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip\Internal\Dispatcher;

/**
 * Event dispatcher class
 *
 */
class EventDispatcher extends EventListener
{
    /**
     * Trigger an event
     *
     * @param string $eventName Event name
     * @param array $data Data for event processing
     * @return null
     */
    public function handle($eventName, $data = [])
    {
        if (!isset($this->listeners[$eventName])) {
            return null;
        }

        if (!isset($this->sortedListeners[$eventName])) {
            ksort($this->listeners[$eventName]);
            $this->sortedListeners[$eventName] = true;
        }

        reset($this->listeners[$eventName]);
        do {
            foreach (current($this->listeners[$eventName]) as $callable) {
                call_user_func($callable, $data);
            }
        } while (next($this->listeners[$eventName]) !== false);
    }
}
