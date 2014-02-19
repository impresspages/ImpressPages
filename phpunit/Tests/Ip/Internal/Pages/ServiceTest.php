<?php

namespace Tests\Ip\Internal\Pages;

use Ip\Internal\Pages\Service;
use PhpUnit\Helper\TestEnvironment;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }

    public function testCreateMenu()
    {
        $menu = Service::createMenu('en', 'testMenu', 'Test menu');

        $this->assertNotEmpty($menu);
    }
} 
