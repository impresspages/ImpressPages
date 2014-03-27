<?php

namespace Tests\Ip\Internal\Content;

use PhpUnit\Helper\TestEnvironment;
use \Ip\Internal\Content\Service;
use \Ip\Internal\Pages\Service as PagesService;


class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        TestEnvironment::setup();
    }

    public function testRemoveRevision()
    {
        /*
         * Given I have a test page
         */
        $menuName = PagesService::createMenu('en', 'testMenu', 'Test menu');
        $menu = PagesService::getMenu('en', $menuName);

        $testPageId = PagesService::addPage($menu['id'], 'Test page');

        /*
         * with first revision containing few widgets
         */
        $firstRevisionId = \Ip\Internal\Revision::createRevision($testPageId, true);
        $widgetList = array();
        $widgetList[] = Service::createWidget('Text', array('text' => 'Widget 1'), 'default', $firstRevisionId, 0, 'test', 1);
        $widgetList[] = Service::createWidget('Text', array('text' => 'Widget 2'), 'default', $firstRevisionId, 0, 'test', 1);
        $widgetList[] = Service::createWidget('Text', array('text' => 'Widget 3'), 'default', $firstRevisionId, 0, 'test', 1);

        $widgetCount = ipDb()->selectValue('widget', 'COUNT(*)', array('revisionId' => $firstRevisionId));
        $this->assertEquals(3, $widgetCount);

        /*
         * When first revision is duplicated
         */
        $secondRevisionId = \Ip\Internal\Revision::duplicateRevision($firstRevisionId);

        /*
         * and second revision is published
         */
        $secondRevisionId = \Ip\Internal\Revision::publishRevision($secondRevisionId);

        /*
         * and first revision is deleted
         */
        Service::removeRevision($firstRevisionId);

        /*
         * Then first revision widgets should not be in a database
         */
        $widgetCount = ipDb()->selectValue('widget', 'COUNT(*)', array('revisionId' => $firstRevisionId));
        $this->assertEquals(0, $widgetCount);

        foreach ($widgetList as $widgetId) {
            $this->assertEmpty(ipDb()->selectValue('widget', 'id', array('id' => $widgetId)));
        }

        /*
         * and new revision should contain copies of those widgets
         */
        $widgetCount = ipDb()->selectValue('widget', 'COUNT(*)', array('revisionId' => $secondRevisionId));
        $this->assertEquals(0, $widgetCount);
    }
} 
