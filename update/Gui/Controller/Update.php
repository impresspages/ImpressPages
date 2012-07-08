<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Gui\Controller;

class Update extends \IpUpdate\Gui\Controller
{
    
    public function getStatusAction() 
    {
        $updateService = new \IpUpdate\Library\Service(__DIR__.'/../../../');

        if ($updateService->isLocked()) {
            $html = '';
        } else {
            $currentVersion = $updateService->getCurrentVersion();
            $view = \IpUpdate\Gui\View::create('Update/overview.php');
            $availableVersions = $updateService->getAvailableVersions();
            $newVersion = array_pop($availableVersions);
            $view->assign('currentVersion', $currentVersion);
            $view->assign('newVersion', $newVersion);
            $html = $view->render();
        }
        
        $data = array(
            'html' => $html
        );

        $this->returnJson($data);
    }
    
    
    public function runAction()
    {
        
    }
    
    public function rollbackAction()
    {
        
    }
    
}