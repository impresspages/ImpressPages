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

        $dir = $this->removeTrailingSlash($dir); //remove trailing slash
        $parentDir = $this->getParentDir($dir);
         

        if (!file_exists($parentDir) || !is_dir($parentDir)) {
            $this->createWritableDir($parentDir);
        }

        if (!is_writable($parentDir)) {
            $this->throwWritePermissionsError($parentDir);
        }

        mkdir($dir);
    }


    /**
     * Make directory or file and all subdirs and files writable
     * @param string $dir
     * @param int $permissions eg 0755. ZERO IS REQUIRED. Applied only to files and folders that are not writable.
     * @return boolean
     */
    function makeWritable($path, $permissions = null)
    {
        if ($permissions == null) {
            $permissions = $this->getParentPermissions($path);
        }
        
        $answer = true;
        if(!file_exists($path)) {
            return false;
        }

        if (!is_writable($path)) {
            $success = chmod($path, $permissions);
            if (!is_writable($path)) {
                $this->throwWritePermissionsError($path);
            }
        }

        if (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if($file == ".." || $file == ".") {
                        continue;
                    }
                    if (is_dir($path.'/'.$file)) {
                        $this->makeWritable($path.'/'.$file, $permissions);
                    } else {
                        if (!is_writable($path.'/'.$file)) {
                            chmod($path.'/'.$file, $permissions);
                        }
                        if (!is_writable($path.'/'.$file)) {
                            $this->throwWritePermissionsError($path.'/'.$file);
                        }
                    }
                }
                closedir($handle);
            }
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
    
    private function getParentPermissions($path)
    {
        return fileperms($this->getParentDir($path));
    }
    
    private function getParentDir($path)
    {
        $path = $this->removeTrailingSlash($path);
        $parentDir = substr($path, 0, strrpos($path, '/') + 1);
        return $parentDir;
    }
    
    private function removeTrailingSlash($path)
    {
        return preg_replace('{/$}', '', $path);
    }
}