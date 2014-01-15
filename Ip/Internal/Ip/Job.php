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
        if (count($parts) != 2) {
            ipLog()->warning('Request.invalidControllerAction: {action}', array('action' => $actionString));
            return;
        }

        return array(
            'plugin' => $parts[0],
            'controller' => $controller,
            'action' => $parts[1],
        );
    }
} 