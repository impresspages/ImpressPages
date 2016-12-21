<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip;

/**
 * Event dispatcher class
 *
 */
class Dispatcher
{
    protected $eventDispatcher;
    protected $jobDispatcher;
    protected $filterDispatcher;
    protected $slotDispatcher;

    public function __construct()
    {
        $this->eventDispatcher = new \Ip\Internal\Dispatcher\EventDispatcher();
        $this->jobDispatcher = new \Ip\Internal\Dispatcher\JobDispatcher();
        $this->filterDispatcher = new \Ip\Internal\Dispatcher\FilterDispatcher();
        $this->slotDispatcher = new \Ip\Internal\Dispatcher\SlotDispatcher();
    }

    /**
     * Register event listener
     *
     * @param string $name Event name
     * @param string $callable Callable method name. This method is called on specified event.
     * @param int $priority Event priority. Lower number means higher priority.
     */
    public function addEventListener($name, $callable, $priority = 50)
    {
        $this->eventDispatcher->addListener($name, $callable, $priority);
    }

    /**
     * Register filter listener
     *
     * @param string $name Filter name
     * @param callable $callable Method name. This method is called on specified event.
     * @param int $priority Filter priority. Lower number means higher priority.
     */
    public function addFilterListener($name, $callable, $priority = 50)
    {
        $this->filterDispatcher->addListener($name, $callable, $priority);
    }

    /**
     * Register job listener
     *
     * @param string $name job name
     * @param callable $callable method name. This method is called by specified job.
     * @param int $priority . Lower number means higher priority. Only the job with highest priority is processed.
     */
    public function addJobListener($name, $callable, $priority = 50)
    {
        $this->jobDispatcher->addListener($name, $callable, $priority);
    }

    /**
     * Bind to a slot generation event
     *
     * @param string $slot Slot name
     * @param string $callable Callable method
     * @param int $priority Filter priority. Lower number means higher priority.
     * @throws Exception
     */
    public function bindSlot($slot, $callable, $priority = 50)
    {
        $this->slotDispatcher->addListener($slot, $callable, $priority);
    }

    /**
     * Filter a value
     *
     * @param string $eventName filter Event name
     * @param mixed $value Data for filtering
     * @param array $data Additional information which may be used for filter processing
     * @return mixed
     */
    public function filter($eventName, $value, $data = array())
    {
        return $this->filterDispatcher->filter($eventName, $value, $data);
    }

    /**
     * Execute a job
     *
     * @param $eventName
     * @param array $data
     * @return mixed|null
     */
    public function job($eventName, $data = array())
    {
        return $this->jobDispatcher->handle($eventName, $data);
    }

    /**
     * Render slot
     *
     * @param $eventName
     * @param array $data
     * @return mixed|null
     */
    public function slot($eventName, $data = array())
    {
        return $this->slotDispatcher->handle($eventName, $data);
    }

    /**
     * Trigger an event
     *
     * @param string $eventName Event name
     * @param array $data Data for event processing
     * @return null
     */
    public function event($eventName, $data = array())
    {
        $this->eventDispatcher->handle($eventName, $data);
    }


    /**
     * @ignore
     */
    public function _bindApplicationEvents()
    {
        // Parse event files:
        $coreModules = \Ip\Internal\Plugins\Model::getModules();
        foreach ($coreModules as $module) {
            $this->bindPluginEvents($module, '\Ip\Internal');
        }

        if (ipConfig()->database()) {
            $plugins = \Ip\Internal\Plugins\Service::getActivePluginNames();
            foreach ($plugins as $plugin) {
                $this->bindPluginEvents($plugin);
            }
        }
    }

    /**
     * @ignore
     */
    public function _bindInstallEvents()
    {
        $this->bindPluginEvents('Install');
    }

    private function bindPluginEvents($plugin, $namespace = '\Plugin')
    {
        $this->bindPluginEventType($plugin, 'event', $namespace);
        $this->bindPluginEventType($plugin, 'filter', $namespace);
        $this->bindPluginEventType($plugin, 'job', $namespace);
        $this->bindPluginEventType($plugin, 'slot', $namespace);
    }

    private function bindPluginEventType($plugin, $type, $namespace)
    {
        $className = $namespace . '\\' . $plugin . '\\' . ucfirst($type);
        if (!class_exists($className)) {
            return false;
        }

        $class = new \ReflectionClass($className);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        $listenerName = $type . 'Dispatcher';
        $listener = $this->{$listenerName};

        foreach ($methods as $method) {
            if ($method->isStatic()) {
                $info = $this->extractEventNamePriority($method->getName());
                $listener->addListener($info['name'], "{$className}::{$info['method']}", $info['priority']);
            } elseif (ipConfig()->isDevelopmentEnvironment()) {
                throw new \Ip\Exception(esc("{$plugin}\\{$type}::{$method->getName()} must be static."));
            }
        }
        return null;
    }

    private function extractEventNamePriority($methodName)
    {
        $info = array(
            'name' => $methodName,
            'method' => $methodName,
            'priority' => 50,
        );

        $lastUnderscore = strrpos($methodName, '_');
        if ($lastUnderscore) {
            $priority = substr($methodName, $lastUnderscore + 1);
            if (ctype_digit($priority)) {
                $info['name'] = substr($methodName, 0, $lastUnderscore);
                $info['priority'] = (int)$priority;
            }
        }

        return $info;
    }

}
