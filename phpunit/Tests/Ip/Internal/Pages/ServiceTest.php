<?php

namespace Tests\Ip\Internal\Pages;

use PhpUnit\Helper\TestEnvironment;
use \Ip\Internal\Pages\Service;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }

    public function testCreatePage()
    {

    }

    public function testCreateMenu()
    {
        $menu = Service::createMenu('en', 'testMenu', 'Test menu');

        $this->assertNotEmpty($menu);
    }
} 
