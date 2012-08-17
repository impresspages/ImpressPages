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
     * @param string $widgetName
     * @param string $block
     */
    public function addWidget($widgetName, $block = 'main')
    {
        $this->testCase->waitForElementPresent('css=#ipAdminWidgetButton-'.$widgetName);
        $this->testCase->dragAndDropToObject('css=#ipAdminWidgetButton-'.$widgetName, 'css=#ipBlock-'.$block);
        $this->testCase->waitForElementPresent('css=.ipActionWidgetCancel');
    }

    public function cancelWidget()
    {
        $this->testCase->click('css=.ipActionWidgetCancel');
        $this->testCase->waitForElementNotPresent('css=.ipActionWidgetCancel');
    }



}