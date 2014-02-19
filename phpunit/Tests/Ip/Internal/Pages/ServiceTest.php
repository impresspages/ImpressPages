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

        $menu = Service::getMenu('en', 'testMenu');
        $this->assertNotEmpty($menu);
        $this->assertEquals('Test menu', $menu['navigationTitle']);

        Service::deletePage($menu['id']);

        $menu = Service::getMenu('en', 'testMenu');
        $this->assertEmpty($menu);
    }

    public function testCreatePage()
    {
        $pageId = Service::addPage(0, 'Test page');
        $this->assertNotEmpty($pageId);

        $page = Service::getPage($pageId);
        $this->assertNotEmpty($page);
        $this->assertEquals('Test page', $page['pageTitle']);

        Service::deletePage($pageId);

        $page = Service::getPage($pageId);
        $this->assertEmpty($page);
    }
} 
