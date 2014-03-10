<?php

namespace Tests\Ip;

use PhpUnit\Helper\TestEnvironment;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }


    public function testStaticRoute()
    {
        $router = new \Ip\Router();
        $context = array(
            'plugin' => 'Test',
            'controller' => 'PublicController',
        );

        $routes['static-page'] = 'page';
        $router->addRoutes($routes, $context);

        $result = $router->match('static-page');

        $this->assertNotEmpty($result);
        $this->assertEquals('Test',             $result['plugin']);
        $this->assertEquals('PublicController', $result['controller']);
        $this->assertEquals('page',             $result['action']);

        $result = $router->match('static-page/super');

        $this->assertEmpty($result);
    }

    public function testStaticRouteWithPluginName()
    {
        $router = new \Ip\Router();
        $context = array(
            'plugin' => 'Test',
            'controller' => 'PublicController',
        );

        $routes['static-page'] = array(
            'plugin' => 'MyTest',
            'action' => 'page',
        );
        $router->addRoutes($routes, $context);

        $result = $router->match('static-page');

        $this->assertNotEmpty($result);
        $this->assertEquals('MyTest',           $result['plugin']);
        $this->assertEquals('PublicController', $result['controller']);
        $this->assertEquals('page',             $result['action']);
    }

    public function testStaticRouteCallable()
    {
        $router = new \Ip\Router();
        $context = array(
            'plugin' => 'Test',
            'controller' => 'PublicController',
        );

        $routes['static-page'] = function() {
            return 'Hello!';
        };

        $router->addRoutes($routes, $context);

        $result = $router->match('static-page');

        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result['action']);
        $this->assertTrue(is_callable($result['action']));
        $this->assertEquals('Hello!', call_user_func($result['action']));
    }

    public function testStaticRouteWithPlaceholder()
    {
        $router = new \Ip\Router();
        $context = array(
            'plugin' => 'Test',
            'controller' => 'PublicController',
        );

        $routes['hello/{world}'] = 'hello';

        $router->addRoutes($routes, $context);

        $result = $router->match('hello/coder');

        $this->assertNotEmpty($result);
        $this->assertEquals('Test',             $result['plugin']);
        $this->assertEquals('PublicController', $result['controller']);
        $this->assertEquals('hello',            $result['action']);
        $this->assertEquals('coder',            $result['world']);
    }

    public function testRouteFullFormat()
    {
        $router = new \Ip\Router();
        $context = array(
            'plugin' => 'Test',
            'controller' => 'PublicController',
        );

        $routes[] = array(
            'path' => 'hello{/world}',
            'action' => 'hello',
            'name' => 'hello',
        );

        $router->addRoutes($routes, $context);

        $result = $router->match('hello/coder');

        $this->assertNotEmpty($result);
        $this->assertEquals('Test',             $result['plugin']);
        $this->assertEquals('PublicController', $result['controller']);
        $this->assertEquals('hello',            $result['action']);
        $this->assertEquals('coder',            $result['world']);


        $uri = $router->generate('hello', array('world' => 'underworld'));
        $this->assertEquals('hello/underworld', $uri);


        $uri = $router->generate('hello');
        $this->assertEquals('hello', $uri);
    }
}
