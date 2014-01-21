<?php

namespace Tests\Functional;

use PhpUnit\Helper\TestEnvironment;

class WidgetTitleTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setup();
    }

    /**
     * @group Sauce
     * @group Selenium
     */
    public function testTitleWidget()
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

        //asset we have one and only one Title widget
        $this->assertEquals(1, count($titleWidgets));


//        nice way to populate content, but doesn't work on Firefox yet
//        $page->executeScript('var dispatchTextEvent = function(target, initTextEvent_args) {
//          var e = document.createEvent("TextEvent");
//          e.initTextEvent.apply(e, Array.prototype.slice.call(arguments, 1));
//          target.dispatchEvent(e);
//        };');
//        $page->executeScript('dispatchTextEvent(document.activeElement, \'textInput\', true, true, null, \'h\', 0)');

        //Text we are going to add to the title widget
        $testText = 'Sample text';

        $session->wait(10000, "typeof $ !== 'undefined' && $('#ipWidgetTitleControls').is(':visible')"); //wait for widget to init
        $session->executeScript('tinyMCE.activeEditor.setContent(\'' . $testText . '\');');

        //change to h2. This is only way I've found to force widget save on selenium
        $h1Link = $page->find('css', '#ipWidgetTitleControls .ipsH:nth-child(2)');
        $h1Link->click();

        //wait for widget reload
        $session->wait(10000, "typeof $ !== 'undefined' && $('#ipWidgetTitleControls').is(':visible')"); //wait for widget to init

        //reload the page
        $session->reload();

        //wait for page to load
        $session->wait(10000, "typeof $ !== 'undefined' && $('#ipBlock-main .ipWidget-Title').length != 0");

        //asset we have one and only one Title widget
        $page = $session->getPage();
        $titleWidgets = $page->findAll('css', '#ipBlock-main .ipWidget-Title');
        $this->assertEquals(1, count($titleWidgets));

        //check if we have the same text that we have stored before
        $titleWidgetText = $session->evaluateScript("return $('#ipBlock-main .ipWidget-Title h2').text()");
        $this->assertEquals($testText, $titleWidgetText);

        //REMOVE THE WIDGET
        $session->executeScript("$('#ipBlock-main .ipWidget-Title .ipActionWidgetDelete').css('visibility', 'visible')");
        $deleteLink = $page->find('css', '#ipBlock-main .ipWidget-Title .ipActionWidgetDelete');
        $deleteLink->click();

        //wait while widget disappears
        $session->wait(10000, "typeof $ !== 'undefined' && $('#ipBlock-main .ipWidget').length == 0");

        //refresh the page and check if widget has gone
        $session->reload();

        $session->wait(10000, "typeof $ !== 'undefined' && $('#ipBlock-main').length != 0");
        $exampleContent = $session->evaluateScript("return $('#ipBlock-main .ipbExampleContent').length");
        $this->assertEquals(1, $exampleContent);


    }


}
