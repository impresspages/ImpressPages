<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

class FileSystemTest extends \IpUpdate\PhpUnit\UpdateTestCase
{

    public function testMakeWritable()
    {
        $testDir = TEST_TMP_DIR.'FileSystemTest/';
        
        $fileSystemHelper = new \IpUpdate\PhpUnit\Helper\FileSystem();
        $fileSystemHelper->cpDir(TEST_FIXTURE_DIR.'Library/Model/FileSystem/', $testDir);
        
        
        chmod($testDir.'UnwritableFolder/UnwritableSubfolder/writableFile.txt', 0777);
        chmod($testDir.'UnwritableFolder/UnwritableSubfolder/unwritableFile.txt', 0555);
        chmod($testDir.'UnwritableFolder/UnwritableSubfolder', 0555);
        chmod($testDir.'UnwritableFolder/WritableSubfolder', 0777);
        chmod($testDir.'UnwritableFolder', 0555);
        
        $this->assertEquals('555', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/')));
        $this->assertEquals(false, is_writable($testDir.'UnwritableFolder/'));
        $this->assertEquals(false, is_writable($testDir.'UnwritableFolder/UnwritableSubfolder'));
        $this->assertEquals(true, is_writable($testDir.'UnwritableFolder/WritableSubfolder'));
        $this->assertEquals(false, is_writable($testDir.'UnwritableFolder/UnwritableSubfolder/unwritableFile.txt'));
        $this->assertEquals(true, is_writable($testDir.'UnwritableFolder/UnwritableSubfolder/writableFile.txt'));
        
        $fileSystem = new \IpUpdate\Library\Helper\FileSystem();
        $fileSystem->makeWritable($testDir, 0755);
        
        //assert all dirs are now writable
        $this->assertEquals(true, is_writable($testDir.'UnwritableFolder/'));
        $this->assertEquals(true, is_writable($testDir.'UnwritableFolder/UnwritableSubfolder'));
        $this->assertEquals(true, is_writable($testDir.'UnwritableFolder/WritableSubfolder'));
        $this->assertEquals(true, is_writable($testDir.'UnwritableFolder/UnwritableSubfolder/unwritableFile.txt'));
        $this->assertEquals(true, is_writable($testDir.'UnwritableFolder/UnwritableSubfolder/writableFile.txt'));
        
        //assert writable file permissions hasn't changed
        $this->assertEquals('777', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/UnwritableSubfolder/writableFile.txt')));
        $this->assertEquals('777', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/WritableSubfolder')));
        
        $fileSystem->rm($testDir);
    }

    
    public function testCreateWritableDir()
    {
        $testDir = TEST_TMP_DIR.'newWritableDir';
        $fileSystem = new \IpUpdate\Library\Helper\FileSystem();
        $fileSystem->createWritableDir($testDir);
        
        $this->assertEquals(true, file_exists($testDir));
        $this->assertEquals(true, is_writable($testDir));
        
        $fileSystem->rm($testDir);
    }


}

