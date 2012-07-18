<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library;


class Service
{
    private $cf;
    private $installationDir;
    private $parameters;

    public function __construct($installationDir)
    {
        $this->installationDir = $installationDir;
        $configurationParser = new \IpUpdate\Library\Model\ConfigurationParser();
        $this->cf = $configurationParser->parse($installationDir);
        $this->parameters = new \IpUpdate\Library\Model\ConfigurationParser($this->cf['BASE_DIR'].$this->cf['TMP_FILE_DIR'].'update/');
    }


    /**
     * Start or proceed update process. 
     * @param string $destinationVersion
     * @throws \IpUpdate\Library\UpdateException
     */
    public function proceed()
    {
        $update = new \IpUpdate\Library\Model\Update($this->cf);
        $update->proceed();
    }
    
    /**
     * @throws \IpUpdate\Library\UpdateException
     */
    public function resetLock()
    {
        $update = new \IpUpdate\Library\Model\Update($this->cf);
        $update->resetLock();
    }
    
    public function rollback()
    {
    }
    
    public function getCurrentVersion()
    {
        $update = new \IpUpdate\Library\Model\Update($this->cf);
        return $update->getCurrentVersion();
    }

    public function getDestinationVersion()
    {
        $updateModel = new \IpUpdate\Library\Model\Migration();
        $destinationScript = $updateModel->getDestinationScript($this->getCurrentVersion());
        return $destinationScript->getDestinationVersion();
    }

     
}