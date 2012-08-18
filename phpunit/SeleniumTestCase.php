<?php

namespace PhpUnit;

class SeleniumTestCase extends \PHPUnit_Extensions_SeleniumTestCase
{

    private $installation;

    protected function setup()
    {
        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
        
        $this->setBrowser('*googlechrome');
        $this->setBrowserUrl(TEST_TMP_URL);

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