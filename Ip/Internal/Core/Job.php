<?php


namespace Ip\Internal\Core;

class Job
{
    public static function ipRouteAction_20($info)
    {
        if (!$info['request']->_isWebsiteRoot()) {
            return;
        }

        $req = $info['request']->getRequest();

        if (empty($req)) {
            return;
        }

        $actionString = null;

        if (isset($req['aa'])) {
            $actionString = $req['aa'];
            $controller = 'AdminController';
        } elseif (isset($req['sa'])) {
            $actionString = $req['sa'];
            $controller = 'SiteController';
        } elseif (isset($req['pa'])) {
            $actionString = $req['pa'];
            $controller = 'PublicController';
        }

        if (!$actionString) {
            return;
        }

        $parts = explode('.', $actionString);
        if (count($parts) > 2) {
            ipLog()->warning('Request.invalidControllerAction: {action}', array('action' => $actionString));
            return;
        }

        if (empty($parts[1])) {
            $parts[1] = 'index';
        }

        return array(
            'plugin' => $parts[0],
            'controller' => $controller,
            'action' => $parts[1],
        );
    }

    public static function ipExecuteController_70($info)
    {
        if (!is_callable($info['action'])) {
            $controllerClass = $info['controllerClass'];
            $controller = new $controllerClass();
            if (!$controller instanceof \Ip\Controller) {
                throw new \Ip\Exception($controllerClass . ".php must extend \\Ip\\Controller class.");
            }

            $callableAction = array($controller, $info['action']);
            $reflection = new \ReflectionMethod($controller, $info['action']);

        } else {
            $callableAction = $info['action'];
            $reflection = new \ReflectionFunction($callableAction);
        }

        $parameters = $reflection->getParameters();

        $arguments = array();

        foreach ($parameters as $parameter) {

            $name = $parameter->getName();

            if (isset($info[$name])) {
                $arguments[]= $info[$name];
            } elseif ($parameter->isOptional()) {
                $arguments[]= $parameter->getDefaultValue();
            } else {
                throw new \Ip\Exception("Controller action requires $name parameter", array('route' => $info, 'requiredParameter' => $name));
            }
        }

        return call_user_func_array($callableAction, $arguments);
    }

}
