<?php

/**
 * @package ImpressPages
 *
 *
 */




namespace PhpUnit\Helper;

class FileSystem
{


    public function cpDir( $source, $destination ) {

        $source = $this->removeTrailingSlash($source);
        $destination = $this->removeTrailingSlash($destination);

//        `cp -r $source $destination`;
//        return;
        
        if (is_dir( $source ) ) {
            if (!is_dir($destination)) {
                mkdir($destination);
            }

            $directory = dir( $source );
            while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
                if ( $readdirectory == '.' || $readdirectory == '..' ) {
                    continue;
                }
                $pathDir = $source . '/' . $readdirectory;
                if ( is_dir( $pathDir ) ) {
                    $this->cpDir( $pathDir, $destination . '/' . $readdirectory );
                    continue;
                }
                copy( $pathDir, $destination . '/' . $readdirectory );
            }

            $directory->close();
        } else {
            copy( $source, $destination );
        }
    }



    function chmod($dir, $permissions)
    {
        if (is_link($dir)) {
            /*
             * symlinks are used to make some fake "unwritable" dirs for testing purposes. But ths function should overcome those fake restrictions.
             * How it works:
             * Unit tests creates all infractructure to test.
             * Some tests need unwritable directories. And in these cases just chmod'ing is not enough
             * as some scripts chmod them back automatically and we need to test scenario, when they can't chmod them.
             * For such case symlinks to root owned directory are used.
             */
            unlink($dir); 
            return true;
        }
        
        
        $answer = true;
        if(!file_exists($dir)) {
            return false;
        }

//        system(sprintf("chmod -R %o %s", $permissions, $dir));
//        return;

        $success = chmod($dir, $permissions);
        if (!$success) {
            throw new \Exception("Can't change permissions on ".$dir);
        }

        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if($file == ".." || $file == ".") {
                        continue;
                    }
                    
                    $this->chmod($dir.'/'.$file, $permissions);
                }
                closedir($handle);
            }
        }
        return $answer;
    }

    /**
     * Format unix permissions to human readable format. Eg 755
     * 
     * @param oct $perms
     * @return string
     */
    public function formatPermissions($perms)
    {
        return substr(decoct($perms),2);
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
            if ($dir != TEST_TMP_DIR.'readme.txt' && $dir != TEST_TMP_DIR.'readme.md' && $dir != TEST_TMP_DIR.'.gitignore') {
                unlink($dir);
            }
        }
    }    
    
    private function removeTrailingSlash($path)
    {
        return preg_replace('{/$}', '', $path);
    }        
}