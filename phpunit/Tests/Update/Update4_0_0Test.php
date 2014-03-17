<?php

namespace Tests\Update;

/**
 * @group ignoreOnTravis
 * @group Selenium
 */

class Update4_0_0Test extends \PhpUnit\Helper\MinkTestCase
{

    /**
     * @group Selenium
     */
    public function testUpdateFrom4_0_0()
    {
        $session = $this->session();

        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation('4.0.0'); //development version
        $installation->setDefaultConfig(array('testMode' => 1));
        $installation->install();



        $adminHelper = new \PhpUnit\Helper\User\Admin($session, $installation);

        $adminHelper->login();

        $this->session()->executeScript("$('.ipsAdminMenuBlock').removeClass('hidden');");
        $this->find('.ipsAdminMenuBlockContainer a[title=System]')->click();

        $this->find('.ipsStartUpdate')->click();

        $this->waitForElementPresent('p.bg-success');
        $versionSpan = $this->find('div.page-header small');
        $this->assertEquals(\Ip\Application::getVersion(), $versionSpan->getText());


    }



}
