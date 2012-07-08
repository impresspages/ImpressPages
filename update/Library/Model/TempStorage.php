<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Model;


class TempStorage
{
    private $scripts;
    private $storageDir;
    
    public function __construct($storageDir)
    {
        if (substr($storageDir, -1) != '/') {
            $storageDir .= '/';
        }
        $this->storageDir = $storageDir;
    }
    
    public function getValue($key)
    {
        return get_file_content($this->getFileName($key));
    }
    
    
    public function setValue($key, $value)
    {
        file_put_contents($this->getFileName($key), $value);
    }
    
    public function exist($key)
    {
        return file_exists($this->getFileName($key));
    }
    
    public function remove($key)
    {
        unlink($this->getFileName($key));
    }
    
    private function getFileName($key)
    {
        return $storageDir.$key;
    }
    
}