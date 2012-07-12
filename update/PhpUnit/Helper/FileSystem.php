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
        $source = preg_replace('{/$}', '', $source); //remove trailing slash
        $destination = preg_replace('{/$}', '', $destination); //remove trailing slash
        
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



    private function rm($dir, $depth = 0) {
        
        if (!file_exists($dir)) {
            return;
        }
        
        chmod($dir, 0777);
        
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if($file == ".." || $file == ".") {
                        continue;
                    }
                    
                    $this->rm($dir.'/'.$file);
                }
                closedir($handle);
            }
        }
        
        rm ($dir);

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

}