<?php
/**
 * @package   ImpressPages
 *
 *
 */

class FileSystemTest extends \PhpUnit\GeneralTestCase
{

    public function testMakeWritable()
    {
        $testDir = TEST_TMP_DIR.'FileSystemTest/';

        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->cpDir(TEST_FIXTURE_DIR.'update/Library/Model/FileSystem/', $testDir);


        chmod($testDir.'UnwritableFolder/UnwritableSubfolder/writableFile.txt', 0777);
        chmod($testDir.'UnwritableFolder/UnwritableSubfolder/unwritableFile.txt', 0555);
        chmod($testDir.'UnwritableFolder/UnwritableSubfolder', 0555);
        chmod($testDir.'UnwritableFolder/WritableSubfolder', 0777);
        chmod($testDir.'UnwritableFolder', 0555);


        $this->assertEquals('777', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/UnwritableSubfolder/writableFile.txt')));
        $this->assertEquals('555', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/UnwritableSubfolder/unwritableFile.txt')));
        $this->assertEquals('555', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/UnwritableSubfolder')));
        $this->assertEquals('777', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/WritableSubfolder')));
        $this->assertEquals('555', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder')));


        $fileSystem = new \PhpUnit\Helper\FileSystem2();
        $fileSystem->makeWritable($testDir, 0755);

        //assert writable file permissions hasn't changed
        $this->assertEquals('777', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/UnwritableSubfolder/writableFile.txt')));
        $this->assertEquals('777', $fileSystemHelper->formatPermissions(fileperms($testDir.'UnwritableFolder/WritableSubfolder')));

        $fileSystem->rm($testDir);
    }


    public function testCreateWritableDir()
    {
        $testDir = TEST_TMP_DIR.'newWritableDir';
        $fileSystem = new \PhpUnit\Helper\FileSystem2();
        $fileSystem->createWritableDir($testDir);

        $this->assertEquals(true, file_exists($testDir));
        $this->assertEquals(true, is_writable($testDir));

        $fileSystem->rm($testDir);
    }


}

