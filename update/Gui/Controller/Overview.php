<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Gui\Controller;

class Overview extends \IpUpdate\Gui\Controller
{
    
    public function indexAction() 
    {
        $updateService = new \IpUpdate\Library\Service(__DIR__.'/../../../');
        
        $currentVersion = $updateService->getCurrentVersion();
        $availableVersions = $updateService->getAvailableVersions();
        $newVersion = array_pop($availableVersions);
        $this->view->assign('currentVersion', $currentVersion);
        $this->view->assign('newVersion', $newVersion);
    }
    

}