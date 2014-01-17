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
        $session->wait(10000,"typeof $ !== 'undefined' && $('.ipActionPublish').length != 0");

        //TODO remove all existing widgets

        $adminHelper->addWidget('Title');
        $titleWidgets = $page->findAll('css', '#ipBlock-main .ipWidget-Title');
        $this->assertEquals(1, count($titleWidgets));


    }


}
