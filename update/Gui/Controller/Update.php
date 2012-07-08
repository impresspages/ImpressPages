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
        $this->getRequest()->setLayout(null);
        $data = array();
        $this->returnJson($data);
    }
    
    
    public function runAction()
    {
        
    }
    
    public function rollbackAction()
    {
        
    }
    
}