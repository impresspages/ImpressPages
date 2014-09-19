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
        $installation->setDefaultConfig(array(
            'serviceUrl'        => 'http://test.service.impresspages.org/',
            'themeMarketUrl'    => 'http://local.market.impresspages.org/themes-v1/?version=4',
            'imageMarketUrl'    => 'http://local.market.impresspages.org/images-v1/',
            'pluginMarketUrl'   => 'http://local.market.impresspages.org/plugins-v1/',
            'usageStatisticsUrl'   => 'http://example.com',
        ));
        $installation->install();



        $adminHelper = new \PhpUnit\Helper\User\Admin($session, $installation);

        $adminHelper->login();

        $this->session()->executeScript("$('.ipsAdminMenuBlock').removeClass('hidden');");
        $this->find('.ipsAdminMenuBlockContainer a[title=System]')->click();

        $this->find('.ipsStartUpdate')->click();

        $this->waitForElementPresent('p.alert-success');
    }

}
