<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Model;


class FileSystem
{

    public function createWritableDir($dir)
    {
        if (substr($dir, 0, 1) != '/') {
            throw new \IpUpdate\Library\Exception('Absolute path required', \IpUpdate\Library\Exception::OTHER);
        }
        if ($dir == '/' && !is_writable($dir)) {
            $this->throwWritePermissionsError($dir);
        }

        $dir = preg_replace('{/$}', '', $dir); //remove trailing slash
        $parentDir = substr($dir, 0, strrpos($dir, '/') + 1);
         
        
        if (!file_exists($parentDir) || !is_dir($parentDir)) {
            $this->createWritableDir($parentDir);
        }

        if (!is_writable($parentDir)) {
            $this->throwWritePermissionsError($parentDir);
        }
        
        mkdir($dir);
    }

    
    /**
     * Make directory and all subdirs and files writable
     * @param string $dir
     * @param int $permissions eg 0755. ZERO IS REQUIRED. Applied only to files and folders that are not writable.
     * @return boolean
     */
    function makeDirectoryWritable($dir, $permissions)
    {
        $answer = true;
        if(!file_exists($dir) || !is_dir($dir)) {
            return false;
        }
    
        if (!is_writable($dir)) {
            $success = chmod($dir, $permissions);
            if (!is_writable($dir)) {
                $this->throwWritePermissionsError($dir);
            }
        }
        
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if($file == ".." || $file == ".") {
                    continue;
                }
                if (is_dir($dir.'/'.$file)) {
                    $this->makeDirectoryWritable($dir.'/'.$file, $permissions);
                } else {
                    chmod($dir.'/'.$file, $permissions)
                }
            }
            closedir($handle);
        }
            
    
        return $answer;
    }
    

    private function throwWritePermissionsError($dir)
    {
        $errorData = array (
            'dir' => $dir
        );
        throw new \IpUpdate\Library\UpdateException("Can't write directory", \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
    }
}