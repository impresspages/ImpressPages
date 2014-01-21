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

    protected $eventListeners = array();
    protected $filterListeners = array();
    protected $jobListeners = array();

    /**
     * @var array stores info which handlers are sorted
     */
    protected $sortedEventListeners = array();
    protected $sortedFilterListeners = array();
    protected $sortedJobListeners = array();

    protected $initCompleted = false;

    public function __construct()
    {
        $this->addEventListener('ipInitFinished', array($this, 'registerInit'));
    }

    /**
     * @ignore
     */
    public function registerInit()
    {
        $this->initCompleted = true;
    }

    protected function callableError($callable)
    {
        $backtrace = debug_backtrace();
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
    public function addEventListener($name, $callable, $priority = 50)
    {
        if (!is_callable($callable)) {
            $this->callableError($callable);
        }

        if (!isset($this->eventListeners[$name][$priority])) {
            $this->eventListeners[$name][$priority] = array();
        }

        $this->eventListeners[$name][$priority][] = $callable;
        unset($this->sortedEventListeners[$name]);
    }

    /**
     * Register filter listener
     *
     * @param $name Filter name
     * @param $callable Method name. This method is called on specified event.
     * @param int $priority Filter priority. Lower number means higher priority.
     */
    public function addFilterListener($name, $callable, $priority = 50)
    {
        if (!is_callable($callable)) {
            $this->callableError($callable);
        }

        if (!isset($this->filterListeners[$name][$priority])) {
            $this->filterListeners[$name][$priority] = array();
        }

        $this->filterListeners[$name][$priority][] = $callable;
        unset($this->sortedFilterListeners[$name]);
    }

    /**
     * Register job listener
     * @param $name job name
     * @param $callable method name. This method is called by specified job.
     * @param int $priority. Lower number means higher priority. Only the job with highest priority is processed.
     */
    public function addJobListener($name, $callable, $priority = 50)
    {
        if (!is_callable($callable)) {
            $this->callableError($callable);
        }

        if (!isset($this->jobListeners[$name][$priority])) {
            $this->jobListeners[$name][$priority] = array();
        }

        $this->jobListeners[$name][$priority][] = $callable;
        unset($this->sortedJobListeners[$name]);
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
        $this->addJobListener('ipGenerateSlot' . $slot, $callable, $priority);
    }

    private function checkInitCompleted($eventName)
    {
        return; // TODOX Algimantas: I disabled for refactoring purposes
        if (!$this->initCompleted && $eventName != 'ipInitFinished') {
            $backtrace = debug_backtrace();
            if (isset($backtrace[1]['file']) && isset($backtrace[1]['line'])) {
                $file = ' (Error source: ' . $backtrace[1]['file'] . ' line: ' . $backtrace[1]['line'] . ' )';
            } else {
                $file = '';
            }
            throw new \Ip\Exception("Event notification can't be thrown before system init." . $file);
        }

    }

    /**
     * Filter value
     *
     * @param $eventName filter Event name
     * @param $value Data for filtering
     * @param array $data Additional information which may be used for filter processing
     * @return mixed
     */
    public function filter($eventName, $value, $data = array())
    {
        $this->checkInitCompleted($eventName);

        if (!isset($this->filterListeners[$eventName])) {
            return $value;
        }

        if (!isset($this->sortedFilterListeners[$eventName])) {
            ksort($this->filterListeners[$eventName]);
            $this->sortedFilterListeners[$eventName] = true;
        }

        do {
            foreach (current($this->filterListeners[$eventName]) as $callable) {
                $value = call_user_func($callable, $value, $data);
            }
        } while (next($this->filterListeners[$eventName]) !== false);

        return $value;
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
        $this->checkInitCompleted($eventName);

        if (!isset($this->jobListeners[$eventName])) {
            return null;
        }

        if (!isset($this->sortedJobListeners[$eventName])) {
            ksort($this->jobListeners[$eventName]);
            $this->sortedJobListeners[$eventName] = true;
        }

        do {
            foreach (current($this->jobListeners[$eventName]) as $callable) {
                $result = call_user_func($callable, $data);
                if ($result !== null) {
                    return $result;
                }
            }
        } while (next($this->jobListeners[$eventName]) !== false);

        return null;
    }

    /**
     * Trigger an event
     *
     * @param $eventName Event name
     * @param array $data Data for event processing
     * @return null
     */
    public function event($eventName, $data = array())
    {
        $this->checkInitCompleted($eventName);

        if (!isset($this->eventListeners[$eventName])) {
            return null;
        }

        if (!isset($this->sortedEventListeners[$eventName])) {
            ksort($this->eventListeners[$eventName]);
            $this->sortedEventListeners[$eventName] = true;
        }

        do {
            foreach (current($this->eventListeners[$eventName]) as $callable) {
                call_user_func($callable, $data);
            }
        } while (next($this->eventListeners[$eventName]) !== false);
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

        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {
            $this->bindPluginEvents($plugin);
        }
    }

    private function bindPluginEvents($plugin, $namespace = '\Plugin')
    {
        $this->bindPluginEventType($plugin, 'Event', $namespace);
        $this->bindPluginEventType($plugin, 'Filter', $namespace);
        $this->bindPluginEventType($plugin, 'Job', $namespace);
    }

    private function bindPluginEventType($plugin, $type, $namespace)
    {
        $className = $namespace . '\\' . $plugin . '\\' . $type;
        if (!class_exists($className)) {
            return false;
        }

        $class = new \ReflectionClass($className);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        $addMethod = "add{$type}Listener";

        $events = array();
        foreach ($methods as $method) {


            if ($method->isStatic()) {
                $info = $this->extractEventNamePriority($method->getName());
                $this->$addMethod($info['name'], "{$className}::{$info['method']}", $info['priority']);
            } elseif (ipConfig()->isDevelopmentEnvironment()) {
                throw new \Ip\Exception("{$plugin}\\{$type}::{$method->getName()} must be static.");
            }
        }
        return $events;
    }

    private function extractEventNamePriority($methodName)
    {
        $info = array(
            'name' => $methodName,
            'method' => $methodName,
            'priority' => 50,
        );

        $lastUnderscore =  strrpos($methodName , '_');
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
