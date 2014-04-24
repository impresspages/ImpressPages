<?php

namespace Tests\Ip\Internal\Pages;

use Ip\Internal\Pages\Service;
use Ip\Internal\Pages\Model;
use PhpUnit\Helper\TestEnvironment;
use \Ip\Internal\Content\Service as ContentService;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
        ipContent()->_setCurrentLanguage(ipContent()->getLanguageByCode('en'));
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
        $pageId = Service::addPage(0, 'In Test Page', array('languageCode' => 'en'));
        $this->assertNotEmpty($pageId);

        $page = Service::getPage($pageId);
        $this->assertNotEmpty($page);
        $this->assertEquals('In Test Page', $page['title']);
        $this->assertEquals('in-test-page', $page['urlPath']);

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

        ipPageStorage($pages[3])->set('bump', 'it');

        Service::removeDeletedPage($pages[0]);
        $this->assertNotEmpty(Service::getPage($pages[0]));

        Service::deletePage($pages[0]);
        $this->assertEquals('it', ipPageStorage($pages[3])->get('bump'));

        Service::removeDeletedPage($pages[0]);
        $this->assertNull(ipPageStorage($pages[3])->get('bump'));

        foreach ($pages as $pageId) {
            $this->assertEmpty(Service::getPage($pageId));
        }
    }

    public function testRemovePageRevisions()
    {
        /*
         * When we have a page
         */
        $pageId = Service::addPage(0, 'To be deleted...', array('languageCode' => 'en'));

        /*
         * with two revision
         */
        $firstRevision = \Ip\Internal\Revision::getPublishedRevision($pageId);
        $firstRevisionId = $firstRevision['revisionId'];
        $secondRevisionId = \Ip\Internal\Revision::duplicateRevision($firstRevisionId);

        /*
         * (1) when we delete page
         */
        Service::deletePage($pageId);

        /*
         * revisions should exist.
         */
        $this->assertNotEmpty(ipDb()->selectRow('revision', 'revisionId', array('revisionId' => $firstRevisionId)));
        $this->assertNotEmpty(ipDb()->selectRow('revision', 'revisionId', array('revisionId' => $secondRevisionId)));

        /*
         * (2) When we remove deleted page
         */
        Service::removeDeletedPage($pageId);

        /*
         * revisions should not exist any more.
         */
        $this->assertEmpty(ipDb()->selectRow('revision', 'revisionId', array('revisionId' => $firstRevisionId)));
        $this->assertEmpty(ipDb()->selectRow('revision', 'revisionId', array('revisionId' => $secondRevisionId)));
    }

    public function testChangeUrlPath()
    {
        $menuAlias = Service::createMenu('en', 'testMenu', 'Test menu');
        $menu = Service::getMenu('en', $menuAlias);

        $pages = array();

        /*
         * If we have pages with urls 'docs4', 'docs4/child' and 'docs44'
         */
        $pages['docs4']= Service::addPage($menu['id'], 'Docs4', array('languageCode' => 'en', 'urlPath' => 'docs4'));
        $pages['docs4/child']= Service::addPage($pages['docs4'], 'Docs4 First', array('languageCode' => 'en', 'urlPath' => 'docs4/child'));
        $pages['docs44']= Service::addPage($menu['id'], 'Docs44', array('languageCode' => 'en', 'urlPath' => 'docs44'));

        // helper functions
        $urlPath = function ($key) use ($pages) {
            $page = Service::getPage($pages[$key]);
            return $page['urlPath'];
        };

        foreach ($pages as $path => $pageId) {
            $this->assertEquals($path, $urlPath($path));
        }

        /*
         * And have a widgets with paths 'docs4', 'docs44', 'docs4/child', 'docs4/', 'docs4?expand=true'
         */

        $revisionId = \Ip\Internal\Revision::createRevision($pages['docs44'], true);
        $a = array();
        $b = array();

        $a[]= '<a href="http://localhost/docs4">docs4</a>';
        $b[]= '<a href="http://localhost/docs5">docs4</a>';

        $a[]= '<a href="http://localhost/docs44">docs44</a>';
        $b[]= '<a href="http://localhost/docs44">docs44</a>';

        $a[]= '<a href="http://localhost/docs4/child">docs4/child</a>';
        $b[]= '<a href="http://localhost/docs4/child">docs4/child</a>';

        $a[]= '<a href="https://localhost/docs4/">docs4/</a>';
        $b[]= '<a href="https://localhost/docs5/">docs4/</a>';

        $a[]= '<a href="http://localhost/docs4?expand=true/">docs4?expand=true/</a>';
        $b[]= '<a href="http://localhost/docs5?expand=true/">docs4?expand=true/</a>';

        $a[]= 'http://localhost/docs4';
        $b[]= 'http://localhost/docs5';

        $widgetId = ContentService::createWidget('Text', array('text' => implode("\n\n\n", $a)), 'default', $revisionId, 0, 'test', 1);

        /*
         * When we change 'docs4' page path to 'docs5'
         */
        Model::updatePageProperties($pages['docs4'], array('urlPath' => 'docs5'));

        /*
         * `docs4' page path should be 'docs5'
         */
        $this->assertEquals('docs5', $urlPath('docs4'));

        /*
         * other page paths should not be changed
         */
        $this->assertEquals('docs4/child', $urlPath('docs4/child'));
        $this->assertEquals('docs44', $urlPath('docs44'));

        /*
         * 'docs4' link should be changed to 'docs5'
         */
        $widgetRecord = \Ip\Internal\Content\Model::getWidgetRecord($widgetId);

        $result = explode("\n\n\n", $widgetRecord['data']['text']);

        for ($i = 0; $i < count($b); $i++) {
            $this->assertEquals($b[$i], $result[$i]);
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
