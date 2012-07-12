<?php

class FileSystemTest extends \IpUpdate\PhpUnit\UpdateTestCase
{

    public function testCreateWritableDir()
    {
        $testDir = TMP_DIR.'UnwritableFolder';
        
        $fileSystemHelper = new \IpUpdate\PhpUnit\Helper\FileSystem();
        $fileSystemHelper->cpDir(FIXTURE_DIR.'Library/Model/FileSystem/UnwritableFolder/', TMP_DIR);
        
//         $perms = fileperms(FIXTURE_DIR.'Library/Model/FileSystem/UnwritableFolder/');
//         $readablePermissions = substr(decoct($perms),3);
//         echo $readablePermissions;

//         if (is_writable(FIXTURE_DIR.'Library/Model/FileSystem/UnwritableFolder/')) {
//             ECHO 'WRITABLE';
//         } ELSE {
//             ECHO 'NOT WRITABLE';

//         }
        
    }



}

