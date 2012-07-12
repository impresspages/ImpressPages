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


    private function copyDirectory( $source, $destination ) {
        if ( is_dir( $source ) ) {
            @mkdir( $destination );
            $directory = dir( $source );
            while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
                if ( $readdirectory == '.' || $readdirectory == '..' ) {
                    continue;
                }
                $PathDir = $source . '/' . $readdirectory;
                if ( is_dir( $PathDir ) ) {
                    $this->copyDirectory( $PathDir, $destination . '/' . $readdirectory );
                    continue;
                }
                copy( $PathDir, $destination . '/' . $readdirectory );
            }

            $directory->close();
        }else {
            copy( $source, $destination );
        }
    }

}

