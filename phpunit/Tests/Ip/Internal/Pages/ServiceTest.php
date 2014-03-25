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
        $this->assertEquals('Test menu', $menu['title']);

        Service::deletePage($menu['id']);

        $menu = Service::getMenu('en', 'testMenu');
        $this->assertEmpty($menu);
    }

    public function testCreatePage()
    {
        $pageId = Service::addPage(0, 'Test page', array('languageCode' => 'en'));
        $this->assertNotEmpty($pageId);

        $page = Service::getPage($pageId);
        $this->assertNotEmpty($page);
        $this->assertEquals('Test page', $page['title']);
        $this->assertEquals('test-page', $page['urlPath']);

        $subpageId = Service::addPage($pageId, 'Test subpage');
        $this->assertNotEmpty($subpageId);
        $subpage = Service::getPage($subpageId);
        $this->assertNotEmpty($subpage);
        $this->assertEquals('Test subpage', $subpage['title']);
        $this->assertEquals('test-subpage', $subpage['urlPath']);

        Service::deletePage($pageId);

        $page = Service::getPage($pageId);
        $this->assertEmpty($page);

        $subpage = Service::getPage($subpageId);
        $this->assertEmpty($subpage);
    }

    public function testMovePage()
    {
        $firstPageId = Service::addPage(0, 'First page', array('languageCode' => 'en'));
        $this->assertNotEmpty($firstPageId);

        $firstPage = Service::getPage($firstPageId);
        $this->assertEquals('first-page', $firstPage['urlPath']);

        $secondPageId = Service::addPage(0, 'Second page', array('languageCode' => 'en'));
        $this->assertNotEmpty($secondPageId);

        $secondPage = Service::getPage($secondPageId);
        $this->assertEquals('second-page', $secondPage['urlPath']);

        Service::movePage($secondPageId, $firstPageId, 1);
        $secondPage = Service::getPage($secondPageId);
        $this->assertEquals($firstPageId, $secondPage['parentId']);
        $this->assertEquals('second-page', $secondPage['urlPath']);

        $newSecondPageId = Service::addPage(0, 'Second page', array('languageCode' => 'en'));
        $this->assertNotEmpty($newSecondPageId);

        $newSecondPage = Service::getPage($newSecondPageId);
        $this->assertEquals('second-page-2', $newSecondPage['urlPath']);

        Service::movePage($newSecondPageId, $firstPageId, 2);
        $newSecondPage = Service::getPage($newSecondPageId);
        $this->assertEquals('second-page-2', $newSecondPage['urlPath']);

        Service::deletePage($firstPageId);
    }

    public function testDeletePage()
    {
        $pageId = Service::addPage(0, 'To be deleted...', array('languageCode' => 'en'));
        Service::deletePage($pageId);

        $page = Service::getPage($pageId);
        $this->assertEmpty($page);

        $page = ipDb()->selectRow('page', '*', array('id' => $pageId));
        $this->assertNotEmpty($page);

        $this->assertTrue($this->isNear($page['deletedAt']));
    }

    public function testRemoveDeletedBefore()
    {
        $pages = array();
        $pages[]= Service::addPage(0, 'To be deleted...', array('languageCode' => 'en'));
        $pages[]= Service::addPage($pages[0], 'First', array('languageCode' => 'en'));
        $pages[]= Service::addPage($pages[0], 'Second', array('languageCode' => 'en'));
        $pages[]= Service::addPage($pages[0], 'Third', array('languageCode' => 'en'));
        $pages[]= Service::addPage($pages[0], 'Fourth', array('languageCode' => 'en'));

        Service::deletePage($pages[1]); // this page should be garbage collected
        Service::deletePage($pages[4]); // this page should be garbage collected
        $garbageCollectionTime = strtotime('+1 second');

        sleep(2);
        Service::deletePage($pages[2]); // this page should not be garbage collected


        Service::removeDeletedBefore(date('Y-m-d H:i:s', $garbageCollectionTime));

        $leftovers = ipDb()->selectAll('page', '*', array('parentId' => $pages[0]));
        $this->assertEquals(2, count($leftovers), 'Only two pages should be left');
        $this->assertEquals($pages[2], $leftovers[0]['id']); // this page is deleted but not garbage collected
        $this->assertEquals($pages[3], $leftovers[1]['id']);

        $this->assertNotEmpty(ipDb()->selectRow('page', '*', array('id' => $pages[2])));
        Service::removeDeletedPage($pages[2]);
        $this->assertEmpty(ipDb()->selectRow('page', '*', array('id' => $pages[2])));

        Service::deletePage($pages[0]);
    }

    public function testRemoveDeleted()
    {
        $pages = array();
        $pages[]= Service::addPage(0, 'To be deleted...', array('languageCode' => 'en'));
        $pages[]= Service::addPage($pages[0], 'First', array('languageCode' => 'en'));
        $pages[]= Service::addPage($pages[0], 'Second', array('languageCode' => 'en'));
        $pages[]= Service::addPage($pages[1], 'Third', array('languageCode' => 'en'));
        $pages[]= Service::addPage($pages[3], 'Fourth', array('languageCode' => 'en'));

        Service::removeDeletedPage($pages[0]);
        $this->assertNotEmpty(Service::getPage($pages[0]));

        Service::deletePage($pages[0]);
        Service::removeDeletedPage($pages[0]);

        foreach ($pages as $pageId) {
            $this->assertEmpty(Service::getPage($pageId));
        }

    }

    protected function isNear($actualTime, $expectedTime = null)
    {
        if (!$expectedTime) {
            $expectedTime = time();
        }

        if ($actualTime == date('Y-m-d H:i:s', $expectedTime)) {
            return true;
        }

        $time = strtotime($actualTime);

        return $time > strtotime('-1 minute', $expectedTime) && $time < strtotime('+1 minute', $expectedTime);
    }

}
