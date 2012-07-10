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
        $dir = preg_replace('{/$}', '', $dir); //remove trailing slash
        $parentDir = substr($dir, 0, strrpos($dir, '/'));
        if (!file_exists($parentDir) || !is_dir($parentDir)) {
            $this->createFolder($parentDir);
        }
        
        if (!is_writable($dir)) {
            $errorData = array (
                'dir' => $dir
            );
            throw new \IpUpdate\Library\UpdateException("Can't write directory", \IpUpdate\Library\UpdateException::WRITE_PERMISSION, $errorData);
        }
        
        mkdir($dir);
    }
    
}