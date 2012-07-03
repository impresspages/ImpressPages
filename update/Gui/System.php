<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Gui;

class System
{
    private $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function execute()
    {
        
    }
    
}