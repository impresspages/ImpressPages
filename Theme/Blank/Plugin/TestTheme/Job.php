<?php


namespace Plugin\Test;


class Job
{
    public static function ipRouteAction($info)
    {
        if ($info['relativeUri'] == 'hello') {
            return array(
               'plugin' => 'Test',
               'controller' => 'PublicController',
               'action' => 'hello',
            );
        }
    }
} 