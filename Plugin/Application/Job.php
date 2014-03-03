<?php


namespace Plugin\Application;


class Job
{
    public static function ipRouteAction($info)
    {
        if ($info['relativeUri'] == 'hello') {
            return array(
               'plugin' => 'Application',
               'controller' => 'PublicController',
               'action' => 'hello',
            );
        }
    }
}
