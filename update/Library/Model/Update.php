<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Model;


class Update
{

    private $cf;
    
    
    /**
     * @var \IpUpdate\Library\Model\TempStorage
     */
    private $tempStorage;
    
    public function __construct($config)
    {
        $this->cf = $config;
        $this->tempStorage = new \IpUpdate\Library\Model\TempStorage($this->cf['BASE_DIR'].$this->cf['TMP_FILE_DIR'].'update/'); 
    }

    public function proceed()
    {
        if ($this->tempStorage->exist('inProgress')) {
            throw new \IpUpdate\Library\UpdateException("Update is in progress", \IpUpdate\Library\UpdateException::IN_PROGRESS);
        }
        
        $this->tempStorage->setValue('inProgress', 1);
        
        $db = new Db();
        $conn = $db->connect($this->cf);
        
        
        $tempStorage->remove('inProgress');
        $db->disconnect();
        
    }
    
    public function resetLock()
    {
        $this->tempStorage->remove('inProgress');
    }
    
    private function getFoldersToReplace() 
    {
        return array (
            'ip_cms',
            $this->cf['LIBRARY_DIR']
        );
    }
    
    private function getFilesToReplace()
    {
        return array (
            'admin.php',
            'index.php',
            'ip_backend_frames.php',
            'ip_backend_worker.php',
            'ip_cron.php',
            'ip_license.html',
            'sitemap.php'
        );
    }
}