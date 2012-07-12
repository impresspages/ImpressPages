<?php

class FileSystemTest extends \IpUpdate\PhpUnit\UpdateTestCase
{

    public function testCreateWritableDir()
    {
        $perms = fileperms(FIXTURE_DIR.'Library/Model/FileSystem/UnwritableFolder/');
        $readablePermissions = substr(decoct($perms),3);
        echo $readablePermissions;

        if (is_writable(FIXTURE_DIR.'Library/Model/FileSystem/UnwritableFolder/')) {
            ECHO 'WRITABLE';
        } ELSE {
            ECHO 'NOT WRITABLE';

        }
        $this->copyDirectory(FIXTURE_DIR.'Library/Model/FileSystem/UnwritableFolder/', TMP_DIR.'');
         
    }



}

