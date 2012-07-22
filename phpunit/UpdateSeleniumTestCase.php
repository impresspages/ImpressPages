<?php

namespace IpUpdate\PhpUnit;

//class UpdateTestCase extends \PHPUnit_Extensions_Database_TestCase
class UpdateSeleniumTestCase extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected function setup()
    {
        $fileSystemHelper = new \IpUpdate\PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
        
        $this->setBrowser('*firefox');
        $this->setBrowserUrl('http://localhost/');
    }
    
    protected function tearDown()
    {
        $fileSystemHelper = new \IpUpdate\PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
    }
    
    
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/guestbook-seed.xml');
    }
    
}