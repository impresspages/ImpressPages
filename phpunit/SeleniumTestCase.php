<?php

namespace PhpUnit;

use PhpUnit\Helper\TestEnvironment;

class SeleniumTestCase extends \PHPUnit_Extensions_SeleniumTestCase
{

    protected $captureScreenshotOnFailure;
    protected $screenshotPath;
    protected $screenshotUrl;
    private $installation;

    protected function setup()
    {
        $this->captureScreenshotOnFailure = TEST_CAPTURE_SCREENSHOT_ON_FAILURE;
        $this->screenshotPath = TEST_SCREENSHOT_PATH;
        $this->screenshotUrl = TEST_SCREENSHOT_URL;

        TestEnvironment::initCode();
        TestEnvironment::cleanupFiles();

        $this->setBrowser('*firefox'); //*googlechrome (can't manipulate file input)
        $this->setBrowserUrl(TEST_TMP_URL);
        $this->setTimeout(10);
//        $driver = $this->getDriver(array('*firefox'));
    }
    
    protected function tearDown()
    {
        TestEnvironment::cleanupFiles();
    }
    
    protected function assertNoErrors() 
    {
        $this->assertElementNotPresent('css=.error');
        $this->assertElementNotPresent('css=.warning');
        $this->assertElementNotPresent('css=.notice');
        $this->assertTextNotPresent('on line');
    }

    /**
     * @return \PhpUnit\Helper\Installation
     */
    public function getInstallation()
    {
        if (!$this->installation) {
           $installation = new \PhpUnit\Helper\Installation(); //development version
           $installation->install();
            $this->installation = $installation;
        }
        return $this->installation;
    }
}