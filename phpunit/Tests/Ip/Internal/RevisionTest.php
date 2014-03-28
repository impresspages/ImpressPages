<?php

namespace Tests\Ip\Internal;

use PhpUnit\Helper\TestEnvironment;
use \Ip\Internal\Content\Service as ContentService;
use \Ip\Internal\Pages\Service as PagesService;


class RevisionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        TestEnvironment::setup();
    }

    public function testDuplicateRevision()
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
        $widgetList[] = ContentService::createWidget('Text', array('text' => 'Widget 1'), 'default', $firstRevisionId, 0, 'test', 1);
        $widgetList[] = ContentService::createWidget('Text', array('text' => 'Widget 2'), 'default', $firstRevisionId, 0, 'test', 1);
        $widgetList[] = ContentService::createWidget('Text', array('text' => 'Widget 3'), 'default', $firstRevisionId, 0, 'test', 1);

        $widgetCount = ipDb()->selectValue('widget', 'COUNT(*)', array('revisionId' => $firstRevisionId));
        $this->assertEquals(3, $widgetCount);

        /*
         * When first revision is duplicated
         */
        $secondRevisionId = \Ip\Internal\Revision::duplicateRevision($firstRevisionId);

        /*
         * second revision should have the same widgets
         */
        $widgetCount = ipDb()->selectValue('widget', 'COUNT(*)', array('revisionId' => $secondRevisionId));
        $this->assertEquals(3, $widgetCount);
    }
} 
