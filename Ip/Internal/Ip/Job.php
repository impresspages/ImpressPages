<?php


namespace Ip\Internal\Ip;

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
        $plugin = array_shift($parts);
        if (isset($parts[0])) {
            $action = $parts[0];
        }


        return array(
            'plugin' => $plugin,
            'controller' => $controller,
            'action' => $action,
        );
    }
} 