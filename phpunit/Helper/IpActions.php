<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace PhpUnit\Helper;


/**
 * Class to manipulate widgets. Supports only one managed widget at the time
 */
class IpActions
{
    /**
     * @var \PHPUnit_Extensions_SeleniumTestCase
     */
    private $testCase;
    private $installation;

    /**
     * @param \PHPUnit_Extensions_SeleniumTestCase $testCase
     * @param Installation $installation
     */
    public function __construct(\PHPUnit_Extensions_SeleniumTestCase $testCase, \PhpUnit\Helper\Installation $installation) {
        $this->testCase = $testCase;
        $this->installation = $installation;
    }


    /**
     * @param Installation $installation
     */
    public function login()
    {
        $this->testCase->open($this->installation->getInstallationUrl().'admin/');
        $loggedIn = true;

        try  {
            $this->testCase->assertElementNotPresent('css=.ipsLoginButton');
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $loggedIn = false;
        }
        if (!$loggedIn) {
            $this->testCase->type('css=.form-control[name=login]', $this->installation->getAdminLogin());
            $this->testCase->type('css=.form-control[name=password]', $this->installation->getAdminPass());
            $this->testCase->clickAndWait('css=.ipsLoginButton');
        }
        $this->testCase->waitForElementPresent('css=.ipsContentPublish');
    }


    public function selectFirstFileInRepository()
    {
        $this->testCase->waitForElementPresent('css=.ipmBrowserContainer ul li');
        $this->testCase->click('css=.ipmBrowserContainer ul li:first');
        $this->testCase->waitForElementPresent('css=.ipaSelectionConfirm');
        $this->testCase->click('css=.ipaSelectionConfirm');

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
        $this->testCase->click('css=.ipsContentPublish');
        $this->testCase->waitForElementPresent('css=.ipSeleniumProgress');
        $this->testCase->waitForNotVisible('css=.ipSeleniumProgress');
        $this->testCase->waitForElementPresent('css=.ipsContentPublish');
    }

    /**
     * @param string $module
     */
    public function openModule($module)
    {
        switch ($module) {
            case 'system':
                $this->testCase->open($this->installation->getInstallationUrl().'?aa=System.index');
                $this->testCase->waitForElementPresent('css=.ipsClearCache');
                break;
            default:
                throw new \Exception("Unknown error");
                break;
        }
    }

}
