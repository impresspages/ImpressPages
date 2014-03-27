<?php

namespace Tests\Update;

/**
 * @group ignoreOnTravis
 * @group Selenium
 */

class UpdateDevToDevTest extends \PhpUnit\Helper\MinkTestCase
{

    /**
     * @group Selenium
     */
    public function testUpdateDevToDev()
    {
        $session = $this->session();

        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->setDefaultConfig(array('testMode' => 1));
        $installation->install();



        $adminHelper = new \PhpUnit\Helper\User\Admin($session, $installation);

        $adminHelper->login();

        $this->session()->executeScript("$('.ipsAdminMenuBlock').removeClass('hidden');");
        $this->find('.ipsAdminMenuBlockContainer a[title=System]')->click();

        $this->find('.ipsStartUpdate')->click();

        $this->waitForElementPresent('p.alert-success');
    }

}
