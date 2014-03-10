<?php


namespace Plugin\Test;

use Aura\Router\RouterFactory;

class Job
{
    public static function ipRouteAction($info)
    {
        $factory = new RouterFactory();
        $router = $factory->newInstance();
        $router->addValues(array(
            'plugin' => 'Test',
            'controller' => 'PublicController'
        ));

        $router->add('hello', 'hello-{world}')->addValues(array(
            'action' => 'hello'
        ));

        $router->add('Test.blog', 'blog/read/{id}{format}')
            ->addTokens(array(
                'id' => '\d+',
                'format' => '(\.[^/]+)?',
            ))->addValues(array(
                'format' => '.html',
            ));

        $router->add('Test.funky', 'funky/{name}');

        $route = $router->match($info['relativeUri']);

        if ($route) {
            return $route->params;
        }
    }
}
