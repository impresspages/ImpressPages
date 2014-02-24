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
        $this->assertEquals('test-page', $page['urlPath']);

        $subpageId = Service::addPage($pageId, 'Test subpage');
        $this->assertNotEmpty($subpageId);
        $subpage = Service::getPage($subpageId);
        $this->assertNotEmpty($subpage);
        $this->assertEquals('Test subpage', $subpage['pageTitle']);
        $this->assertEquals('test-page/test-subpage', $subpage['urlPath']);

        Service::deletePage($pageId);

        $page = Service::getPage($pageId);
        $this->assertEmpty($page);

        $subpage = Service::getPage($subpageId);
        $this->assertEmpty($subpage);
    }

    public function testMovePage()
    {
        $firstPageId = Service::addPage(0, 'First page');
        $this->assertNotEmpty($firstPageId);

        $firstPage = Service::getPage($firstPageId);
        $this->assertNotEmpty($firstPage);
        $this->assertEquals('first-page', $firstPage['urlPath']);

        $secondPageId = Service::addPage(0, 'Second page');
        $this->assertNotEmpty($secondPageId);

        $secondPage = Service::getPage($secondPageId);
        $this->assertNotEmpty($secondPage);
        $this->assertEquals('second-page', $secondPage['urlPath']);

        Service::movePage($secondPageId, $firstPageId, 1);
        $secondPage = Service::getPage($secondPageId);
        $this->assertNotEmpty($secondPage);
        $this->assertEquals($firstPageId, $secondPage['parentId']);
        $this->assertEquals('first-page/second-page', $secondPage['urlPath']);

        $newSecondPageId = Service::addPage(0, 'Second page');
        $this->assertNotEmpty($newSecondPageId);

        $newSecondPage = Service::getPage($newSecondPageId);
        $this->assertNotEmpty($newSecondPage);
        $this->assertEquals('second-page', $newSecondPage['urlPath']);

        Service::movePage($newSecondPageId, $firstPageId, 2);
        $newSecondPage = Service::getPage($newSecondPageId);
        $this->assertNotEmpty($newSecondPage);
        $this->assertEquals('first-page/second-page-2', $newSecondPage['urlPath']);

        Service::deletePage($firstPageId);
    }

}
