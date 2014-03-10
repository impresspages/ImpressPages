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
        $router->group($context, function($router) {
            $router->get('static-page', 'page');
        });

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
        $router->group($context, function($router) {
                $router->get('static-page', 'MyTest.page');
            });

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
        $router->group($context, function($router) {
                $router->get('callable-static-page', 'page', function() {
                    return 'Hello!';
                });
            });

        $result = $router->match('callable-static-page');

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
        $router->group($context, function($router) {
                $router->get('hello/{world}', 'hello');
            });

        $result = $router->match('hello/coder');

        $this->assertNotEmpty($result);
        $this->assertEquals('Test',             $result['plugin']);
        $this->assertEquals('PublicController', $result['controller']);
        $this->assertEquals('hello',            $result['action']);
        $this->assertEquals('coder',            $result['world']);
    }
}
