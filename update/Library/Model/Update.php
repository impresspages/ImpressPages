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
    
    const STEP_START = 0;
    const STEP_DOWNLOAD_PACKAGE = 1;
    const STEP_CLOSE_WEBSITE = 2;
    const STEP_REMOVE_OLD_FILES = 3;
    const STEP_RUN_MIGRATIONS = 4;
    const STEP_WRITE_NEW_FILES = 5;
    const STEP_PUBLISH_WEBSITE = 6;
    const SETP_FINISH = 7;
    
    const UPDATE_SCRIPT_VERSION = 1;
    
    
    /**
     * @var \IpUpdate\Library\Model\TempStorage
     */
    private $tempStorage;
    
    /**
     * 
     * @var \IpUpdate\Library\Helper\FileSystem
     */
    private $fs;
    
    public function __construct($config)
    {
        $this->cf = $config;
        $this->tempStorage = new \IpUpdate\Library\Model\TempStorage($this->cf['BASE_DIR'].$this->cf['TMP_FILE_DIR'].'update/'); 
        $this->fs = new \IpUpdate\Library\Helper\FileSystem();
    }

    public function proceed()
    {
        if ($this->tempStorage->exist('inProgress')) {
            //existing inProgress variable means that some step is in progress at the moment or has failed. 
            throw new \IpUpdate\Library\UpdateException("Update is in progress", \IpUpdate\Library\UpdateException::IN_PROGRESS);
        }
        
        if (!$this->tempStorage->exist('version')) {
            $this->tempStorage->setValue('version', self::UPDATE_SCRIPT_VERSION);
        }
        
        if ($this->tempStorage->getValue('version') != self::UPDATE_SCRIPT_VERSION) {
            //if script version value is wrong, some other script is executing the upadte. Should be very rare case.
            throw new \IpUpdate\Library\UpdateException("Update is in progress", \IpUpdate\Library\UpdateException::IN_PROGRESS);
        }
        
        if ($this->tempStorage->getValue('version') === false) {
            $this->tempStorage->setValue('vesrion', self::UPDATE_SCRIPT_VERSION);
            $this->tempStorage->setValue('curStep', self::STEP_START);
        }
        $curStep = (int)$this->tempStorage->getValue('curStep');
        
        $this->tempStorage->setValue('inProgress', 1);

        $loop = 0;
        while (true) {
            switch($curStep) {
                case self::STEP_START:
                        $this->stepStart();
                    break;
                case self::STEP_DOWNLOAD_PACKAGE:
                        $this->stepDownloadPackage();
                    break;
                case self::STEP_CLOSE_WEBSITE:
                        $this->stepCloseWebsite();
                    break;
                case self::STEP_REMOVE_OLD_FILES:
                        $this->stepRemoveOldFiles();
                    break;
                case self::STEP_RUN_MIGRATIONS:
                        $this->stepRunMigrations();
                    break;
                case self::STEP_WRITE_NEW_FILES:
                        $this->stepWriteNewFiles();
                    break;
                case self::STEP_PUBLISH_WEBSITE:
                        $this->stepPublishWebsite();
                    break;
                case self::SETP_FINISH:
                        $this->stepFinish();
                    break;
                    
                default:
                    
                    throw new \IpUpdate\Library\Exception("Unknown update state.");
                    break;
            }
            $this->tempStorage->setValue('curStep', $curStep + 1);
            $curStep = $this->tempStorage->getValue('curStep');
            $loop++;
            if ($loop > 100) {
                throw new \IpUpdate\Library\Exception("Infinite loop.");
            }
        }
        
        $this->tempStorage->remove('inProgress');
        
//         $db = new Db();
//         $conn = $db->connect($this->cf);
        
//         $db->disconnect();
        
    }
    
    public function resetLock()
    {
        $this->cleanUp();
    }
    
    
    private function stepStart()
    {
        //do nothing
    }
    
    private function stepDownloadPackage()
    {
        $this->fs->rm();
        $File = $this->cf['BASE_DIR'].'index.php';
        $this->fs->makeWritable($indexFile);
        
    }
    
    private function stepCloseWebsite()
    {
        $indexFile = $this->cf['BASE_DIR'].'index.php';
        $this->fs->makeWritable($indexFile);
        $maintenanceMode = '<?php
header("HTTP/1.1 503 Service Temporarily Unavailable");
header("Status: 503 Service Temporarily Unavailable");
header("Retry-After: 3600");

if (file_exists(__DIR__.\'/maintenance.php\')) {
    require(__DIR__.\'/maintenance.php\');
}
';

        file_put_contents($indexFile, $maintenanceMode);
        exit;        

    }
    
    private function stepRemoveOldFiles()
    {
        
    }
    
    
    private function stepRunMigrations()
    {
        
    }
    
    private function stepWriteNewFiles()
    {
        
    }
    
    private function stepPublishWebsite()
    {
        
    }
    
    private function stepFinish()
    {
        $this->cleanUp();
    }
    
    private function cleanUp()
    {
        $this->tempStorage->remove('inProgress');
        $this->tempStorage->remove('curStep');
        $this->tempStorage->remove('scriptVersion');
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