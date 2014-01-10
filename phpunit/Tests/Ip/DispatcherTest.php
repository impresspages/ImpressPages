<?php

namespace Tests\Ip;

use PhpUnit\Helper\TestEnvironment;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }

    public function tearDown()
    {
        \Ip\ServiceLocator::removeRequest();
    }

    public function testFilterEmpty()
    {
        $result = ipFilter('Filter', 'MyName');
        $this->assertEquals('MyName', $result);
    }

    public function testFilterString()
    {
        \Ip\ServiceLocator::dispatcher()->addFilterListener('TestEvent1', function ($value, $data) {
                return $value . '!';
            }
        );

        $result = ipFilter('TestEvent1', 'MyName');
        $this->assertEquals('MyName!', $result);
    }

    public function testFilterTwoHandlers()
    {
        \Ip\ServiceLocator::dispatcher()->addFilterListener('TestEvent2', function ($result, $data) {
                return 'TestEvent: ' . $result;
            }
        );

        $result = ipFilter('TestEvent2', 'Cat');
        $this->assertEquals('TestEvent: Cat', $result);

        $addItemHandler = function ($defaultResult, $data) {
            $defaultResult[]= $data;
            return $defaultResult;
        };

        \Ip\ServiceLocator::dispatcher()->addFilterListener('TestEvent3', $addItemHandler);
        \Ip\ServiceLocator::dispatcher()->addFilterListener('TestEvent3', $addItemHandler);

        $result = $result = \Ip\ServiceLocator::dispatcher()->filter('TestEvent3', array('Dog'), 'Cat');

        $this->assertEquals(array('Dog', 'Cat', 'Cat'), $result);
    }

//    public function testJob()
//    {
//        // Init environment:
//        TestEnvironment::initCode();
//        \Ip\ServiceLocator::addRequest(new \Ip\Request());
//        \Ip\ServiceLocator::dispatcher()->registerInit();
//
//        $result = \Ip\ServiceLocator::dispatcher()->job('TestJob', array('name' => 'MyName'));
//        $this->assertEquals(NULL, $result);
//
//        // Bind event:
//        \Ip\ServiceLocator::dispatcher()->addJobListener('TestJob', function ($data) {
//                return $data;
//            }
//        );
//
//        $result = \Ip\ServiceLocator::dispatcher()->job('TestJob', array('name' => 'MyName'));
//        $this->assertEquals(array('name' => 'MyName'), $result);
//    }


}