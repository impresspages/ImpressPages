<?php

namespace PhpUnit;

class SeleniumTestCase extends \PHPUnit_Extensions_SeleniumTestCase
{

    protected $captureScreenshotOnFailure;
    protected $screenshotPath;
    protected $screenshotUrl;
    private $installation;

    protected function setup()
    {
        $this->captureScreenshotOnFailure = CAPTURE_SCREENSHOT_ON_FAILURE;
        $this->screenshotPath = SCREENSHOT_PATH;
        $this->screenshotUrl = SCREENSHOT_URL;


        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
        
        $this->setBrowser('*firefox'); //*googlechrome (can't manipulate file input)
        $this->setBrowserUrl(TEST_TMP_URL);
//        $driver = $this->getDriver(array('*firefox'));
    }
    
    protected function tearDown()
    {
        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
    }
    
    protected function assertNoErrors() 
    {
        $this->assertElementNotPresent('css=.error');
        $this->assertElementNotPresent('css=.warning');
        $this->assertElementNotPresent('css=.notice');
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