<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Library;


class Service
{
    private $configuration;
    private $installationDir;
    
    public function __construct($installationDir)
    {
        $this->installationDir = $installationDir;
        $configurationParser = new Model\ConfigurationParser(); 
        $this->configuration = $configurationParser->parse($installationDir);
    }
    
    
    public function update ($destinationVersion)
    {
        
    }
    
    public function getCurrentVersion($installationRoot)
    {
        
    }
    
    public function availableVersions($installationRoot)
    {
        
    }
    
     
}