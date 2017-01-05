<?php
/**
 * @package   ImpressPages
 */


namespace Ip;

/**
 *
 * Information about current request route
 * @package Ip
 */
class Route
{
    protected $action = null;
    protected $controller = null;
    protected $plugin = null;
    protected $environment = 'public';
    protected $controllerClass = null;
    protected $name = null;
    protected $parameters = array();

    const ENVIRONMENT_ADMIN = 'admin';
    const ENVIRONMENT_PUBLIC = 'public';

    /**
     * Get plugin name which controller is being executed
     * @return string
     */
    public function plugin()
    {
        return $this->plugin;
    }

    /**
     * Parameters passed to the controller of the route
     * @return array
     */
    public function parameters()
    {
        return $this->parameters;
    }

    /**
     * One of parameters passed to the controller of the route
     * @return array
     */
    public function parameter($parameterName, $default = null)
    {
        if (!empty($this->parameters[$parameterName])) {
            return $this->parameters[$parameterName];
        }
        return null;
    }

    /**
     * @private
     * @param $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @private
     * @param $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Gets controller file name relative to plugin dir
     *
     * @return string
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function controllerClass()
    {
        if ($this->controllerClass != null) {
            return $this->controllerClass;
        }

        if ($this->plugin == null) {
            return null;
        }

        if (in_array($this->plugin, \Ip\Internal\Plugins\Model::getModules())) {
            $controllerClass = 'Ip\\Internal\\' . $this->plugin . '\\' . $this->controller;
        } else {
            if (!in_array($this->plugin, \Ip\Internal\Plugins\Service::getActivePluginNames())) {
                throw new \Ip\Exception("Plugin '" . esc($this->plugin) . "' doesn't exist or isn't activated.");
            }
            $controllerClass = 'Plugin\\' . $this->plugin . '\\' . $this->controller;
        }

        if (!class_exists($controllerClass)) {
            throw new \Ip\Exception('Requested controller doesn\'t exist. ' . esc($controllerClass));
        }
        $this->controllerClass = $controllerClass;
        return $controllerClass;
    }

    /**
     * @private
     * @param $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }


    /**
     * Gets Controller action
     *
     * @return string controller action name
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * @private
     * Sets MVC controller action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Check if current route is of admin environment
     * @return bool
     */
    public function isAdmin()
    {
        return $this->controller && $this->controller == 'AdminController';
    }

    /**
     * Set route name
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get route name
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    public function csrfMustBeSkipped()
    {
        if ($this->controller == 'PublicController') {
            return true;
        }
        return false;
    }


}
