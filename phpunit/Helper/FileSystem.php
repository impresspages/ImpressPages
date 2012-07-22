<?php

namespace IpUpdate\PhpUnit\Helper;

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


class FileSystem
{


    public function cpDir( $source, $destination ) {
        $source = $this->removeTrailingSlash($source);
        $destination = $this->removeTrailingSlash($destination);
        
        if (is_dir( $source ) ) {
            @mkdir($destination);
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
        $answer = true;
        if(!file_exists($dir)) {
            return false;
        }

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