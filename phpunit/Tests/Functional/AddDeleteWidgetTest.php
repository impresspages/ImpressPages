<?php

namespace Tests\Functional;

use PhpUnit\Helper\TestEnvironment;

class AddDeleteWidgetTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setup();
    }

    /**
     * @group Sauce
     * @group Selenium
     */
    public function testAddRemoveWidgets()
    {
        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();

        $session = \PhpUnit\Helper\Session::factory();
        $page = $session->getPage();

        $adminHelper = new \PhpUnit\Helper\User\Admin($session, $installation);

        $adminHelper->login();

        //These four lines are redundand and has to be removed when installation will be fixed
        $session->wait(10000, "typeof $ !== 'undefined' && $('.ipAdminWidgetsDisable .ipActionPublish').length != 0");
        $duplicateRevisionLink = $page->find('css', '.ipsItemCurrent');
        $duplicateRevisionLink->click();
        $session->wait(10000, "typeof $ !== 'undefined' && $('.ipActionPublish').length != 0");
        //TODO remove all existing widgets

        $adminHelper->addWidget('Title');
        $session->wait(10000, "typeof $ !== 'undefined' && $('#ipBlock-main .ipWidget-Title').length != 0");
        $page = $session->getPage();
        $titleWidgets = $page->findAll('css', '#ipBlock-main .ipWidget-Title');
        $this->assertEquals(1, count($titleWidgets));


//        nice way to populate content, but doesn't work on Firefox yet
//        $page->executeScript('var dispatchTextEvent = function(target, initTextEvent_args) {
//          var e = document.createEvent("TextEvent");
//          e.initTextEvent.apply(e, Array.prototype.slice.call(arguments, 1));
//          target.dispatchEvent(e);
//        };');
//        $page->executeScript('dispatchTextEvent(document.activeElement, \'textInput\', true, true, null, \'h\', 0)');

        $session->wait(5000, "false"); //wait for widget to init
        //$session->executeScript('$(document.activeElement).text(\'TEST\')');
        $session->executeScript('tinyMCE.activeEditor.setContent(\'Sample text\');');
        //$session->executeScript('$(tinyMCE.activeEditor).trigger(\'blur\');');

        $session->wait(5000, "false"); //wait for widget to init

        $h1Link = $page->find('css', '#ipWidgetTitleControls .ipsH');
        $h1Link->click(); //on blur makes widget to save

        $session->wait(5000, "false"); //wait for widget to init

        $duplicateRevisionLink = $page->find('css', 'body');
        $duplicateRevisionLink->click(); //on blur makes widget to save

        $session->wait(10000, "false"); //wait for save



//        $session->reload();

        $page = $session->getPage();
        $titleWidgets = $page->findAll('css', '#ipBlock-main .ipWidget-Title');
        $this->assertEquals(1, count($titleWidgets));

        $session->wait(30000, "false");

    }


}
