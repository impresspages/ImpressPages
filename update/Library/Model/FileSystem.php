<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Model;


class FileSystem
{

    public function createFolder($dir)
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
            $this->createFolder($parentDir);
        }

        if (!is_writable($parentDir)) {
            $this->throwWritePermissionsError($parentDir);
        }
        
        mkdir($dir);
    }


    private function throwWritePermissionsError($dir)
    {
        $errorData = array (
            'dir' => $dir
        );
        throw new \IpUpdate\Library\UpdateException("Can't write directory", \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
    }
}