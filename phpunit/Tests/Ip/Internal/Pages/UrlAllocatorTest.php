<?php

namespace Tests\Ip\Internal\Pages;

use PhpUnit\Helper\TestEnvironment;
use \Ip\Internal\Pages\UrlAllocator;
use \Ip\Internal\Pages\Service as PagesService;

class UrlAllocatorTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }

    public function testAllocatePathForNewPage()
    {
        $menu = PagesService::getMenu('en', 'menu1');

        $page = array(
            'parentId' => $menu['id'],
            'languageCode' => 'en',
            'title' => 'Example Page',
        );

        $path = UrlAllocator::allocatePathForNewPage($page);
        $this->assertEquals('example-page', $path);

        $examplePageId = PagesService::addPage($menu['id'], $page['title']);
        $_page = PagesService::getPage($examplePageId);
        $this->assertEquals('example-page', $_page['urlPath']);

        $path = UrlAllocator::allocatePathForNewPage($page);
        $this->assertEquals('example-page-2', $path);

//        $pageId = PagesService::addPage($menu['id'], $page['title']);
//        $_page = PagesService::getPage($pageId);
//        $this->assertEquals('example-page-2', $_page['urlPath']);
//
//        $path = UrlAllocator::allocatePathForNewPage($page);
//        $this->assertEquals('example-page-3', $path);
//
//        $page['title'] = 'My  precious';
//        $path = UrlAllocator::allocatePathForNewPage($page);
//        $this->assertEquals('my-precious', $path);
//
//        $page['parentId'] = $examplePageId;
//        $path = UrlAllocator::allocatePathForNewPage($page);
//        $this->assertEquals('my-precious', $path);

    }
} 
