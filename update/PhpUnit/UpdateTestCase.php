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
    
    
    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
//        $pdo = new \PDO('mysql:host='.$cf['DB_SERVER'].';dbname='.$cf['DB_DATABASE'], $cf['DB_USERNAME'], $cf['DB_PASSWORD']);
        //return $this->createDefaultDBConnection($pdo, ':memory:');
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/_files/guestbook-seed.xml');
    }    
    
    
    private function cleanDir($dirPath, $depth = 0) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException('$dirPath must be a directory');
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if ($file !== '.' && $file != '..') {
                if (is_dir($file)) {
                    $this->cleanDir($file, $depth + 1);
                } else {
                    if ($file != TEST_TMP_DIR.'readme.txt') {
                        unlink($file);
                    }
                }
            }
        }
        if ($depth != 0) {
            rmdir($dirPath);
        }
    }
    
}