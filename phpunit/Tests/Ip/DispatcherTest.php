<?php

namespace Tests\Ip;

use PhpUnit\Helper\TestEnvironment;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        // Init environment:
        TestEnvironment::initCode();
        \Ip\ServiceLocator::addRequest(new \Ip\Request());
        ipDispatcher()->registerInit();

        $result = ipDispatcher()->filter('TestEvent', 'MyName');
        $this->assertEquals('MyName', $result);

        // Bind event:
        ipDispatcher()->bind('TestEvent', function ($value, $data) {
                return $value . '!';
            }
        );

        $result = ipDispatcher()->filter('TestEvent', 'MyName');
        $this->assertEquals('MyName!', $result);

        ipDispatcher()->replace('TestEvent', function ($result, $data) {
                return 'TestEvent: ' . $result;
            }
        );

        $result = ipDispatcher()->filter('TestEvent', 'Cat');
        $this->assertEquals('TestEvent: Cat', $result);

        $addItemHandler = function ($defaultResult, $data) {
            $defaultResult[]= $data;
            return $defaultResult;
        };

        ipDispatcher()->replace('TestEvent', $addItemHandler);
        ipDispatcher()->bind('TestEvent', $addItemHandler);
        ipDispatcher()->bind('TestEvent', $addItemHandler);

        $result = $result = ipDispatcher()->filter('TestEvent', array('Dog'), 'Cat');

        $this->assertEquals(array('Dog', 'Cat', 'Cat', 'Cat'), $result);
    }

    public function testJob()
    {
        // Init environment:
        TestEnvironment::initCode();
        \Ip\ServiceLocator::addRequest(new \Ip\Request());
        ipDispatcher()->registerInit();

        $result = ipDispatcher()->job('TestJob', array('name' => 'MyName'));
        $this->assertEquals(NULL, $result);

        // Bind event:
        ipDispatcher()->bind('TestJob', function ($data) {
                return $data;
            }
        );

        $result = ipDispatcher()->job('TestJob', array('name' => 'MyName'));
        $this->assertEquals(array('name' => 'MyName'), $result);
    }


}