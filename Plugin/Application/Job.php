<?php


namespace Plugin\Application;


class Job
{
    public static function ipRouteAction($info)
    {
        $router = new \Ip\Router();

        $context = array(
            'plugin' => 'Application',
            'controller' => 'PublicController',
        );

        $router->group($context, function($router) {
            include ipFile('Plugin/Application/routes.php');
        });

        $result = $router->match($info['relativeUri'], ipRequest()->getServer());

        if ($result) {
            return $result;
        }
    }
}
