<?php

namespace IpUpdate\PhpUnit;

//class UpdateTestCase extends \PHPUnit_Extensions_Database_TestCase
class UpdateTestCase extends \PHPUnit_Framework_TestCase
{
    protected function setup()
    {
        $fileSystemHelper = new \IpUpdate\PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $this->cleanDir(TEST_TMP_DIR);
    }
    
    protected function tearDown()
    {
        $fileSystemHelper = new \IpUpdate\PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $this->cleanDir(TEST_TMP_DIR);
    }
    
    
    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/guestbook-seed.xml');
    }    
    
    
    public function cleanDir($dir, $depth = 0) {
        
        if (!file_exists($dir)) {
            return;
        }
        
        $dir = $this->removeTrailingSlash($dir);
        
        chmod($dir, 0777);
        
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if($file == ".." || $file == ".") {
                        continue;
                    }
                    
                    $this->cleanDir($dir.'/'.$file, $depth + 1);
                }
                closedir($handle);
            }
            
            if ($depth != 0) {
                rmdir($dir);
            }
        } else {
            if ($dir != TEST_TMP_DIR.'readme.txt') {
                unlink($dir);
            }
        }
    }    
    
    private function removeTrailingSlash($path)
    {
        return preg_replace('{/$}', '', $path);
    }    
    
    
}