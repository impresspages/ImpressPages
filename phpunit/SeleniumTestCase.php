<?php

namespace PhpUnit;

class SeleniumTestCase extends \PHPUnit_Extensions_SeleniumTestCase
{


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
        $this->assertTextNotPresent('Error');
        $this->assertTextNotPresent('Warning');
        $this->assertTextNotPresent('Notice');
        $this->assertTextNotPresent('NOTICE');
        $this->assertTextNotPresent('NOTICE ');
    }
}