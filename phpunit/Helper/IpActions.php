<?php

namespace PhpUnit\Helper;

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


/**
 * Class to manipulate widgets. Supports only one managed widget at the time
 */
class IpActions
{
    /**
     * @var \PHPUnit_Extensions_SeleniumTestCase
     */
    private $testCase;


    public function __construct(\PHPUnit_Extensions_SeleniumTestCase $testCase) {
        $this->testCase = $testCase;
    }


    /**
     * @param Installation $installation
     */
    public function login(\PhpUnit\Helper\Installation $installation)
    {
        $this->testCase->open($installation->getInstallationUrl().'admin.php');
        $loggedIn = true;

        try  {
            $this->testCase->assertElementNotPresent('css=.loginSubmit');
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $loggedIn = false;
        }
        if (!$loggedIn) {
            $this->testCase->type('css=.loginInput:eq(0)', $installation->getAdminLogin());
            $this->testCase->type('css=.loginInput:eq(1)', $installation->getAdminPass());
            $this->testCase->clickAndWait('css=.loginSubmit');
        }
        $this->testCase->waitForElementPresent('css=.ipActionPublish');
    }



    /**
     * @param string $widgetName
     * @param string $block
     */
    public function addWidget($widgetName, $block = 'main')
    {
        $this->testCase->waitForElementPresent('css=#ipAdminWidgetButton-'.$widgetName);
        $this->testCase->dragAndDropToObject('css=#ipAdminWidgetButton-'.$widgetName, 'css=#ipBlock-'.$block);
        $this->testCase->waitForElementPresent('css=.ipActionWidgetCancel');
    }

    public function confirmWidget()
    {
        $this->testCase->click('css=.ipActionWidgetSave');
        $this->testCase->waitForElementNotPresent('css=.ipActionWidgetSave');
    }

    public function cancelWidget()
    {
        $this->testCase->click('css=.ipActionWidgetCancel');
        $this->testCase->waitForElementNotPresent('css=.ipActionWidgetCancel');
    }

    public function publish()
    {
        $this->testCase->clickAndWait('css=.ipActionPublish');
        $this->testCase->waitForElementNotPresent('css=.ipActionPublish');
    }

}