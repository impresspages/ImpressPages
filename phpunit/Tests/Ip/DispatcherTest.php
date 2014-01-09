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
        $result = ipDispatcher()->filter('Filter', 'MyName');
        $this->assertEquals('MyName', $result);
    }

    public function testFilterString()
    {
        ipDispatcher()->addFilterListener('TestEvent1', function ($value, $data) {
                return $value . '!';
            }
        );

        $result = ipDispatcher()->filter('TestEvent1', 'MyName');
        $this->assertEquals('MyName!', $result);
    }

    public function testFilterTwoHandlers()
    {
        ipDispatcher()->addFilterListener('TestEvent2', function ($result, $data) {
                return 'TestEvent: ' . $result;
            }
        );

        $result = ipDispatcher()->filter('TestEvent2', 'Cat');
        $this->assertEquals('TestEvent: Cat', $result);

        $addItemHandler = function ($defaultResult, $data) {
            $defaultResult[]= $data;
            return $defaultResult;
        };

        ipDispatcher()->addFilterListener('TestEvent3', $addItemHandler);
        ipDispatcher()->addFilterListener('TestEvent3', $addItemHandler);

        $result = $result = ipDispatcher()->filter('TestEvent3', array('Dog'), 'Cat');

        $this->assertEquals(array('Dog', 'Cat', 'Cat'), $result);
    }

//    public function testJob()
//    {
//        // Init environment:
//        TestEnvironment::initCode();
//        \Ip\ServiceLocator::addRequest(new \Ip\Request());
//        ipDispatcher()->registerInit();
//
//        $result = ipDispatcher()->job('TestJob', array('name' => 'MyName'));
//        $this->assertEquals(NULL, $result);
//
//        // Bind event:
//        ipDispatcher()->addJobListener('TestJob', function ($data) {
//                return $data;
//            }
//        );
//
//        $result = ipDispatcher()->job('TestJob', array('name' => 'MyName'));
//        $this->assertEquals(array('name' => 'MyName'), $result);
//    }


}