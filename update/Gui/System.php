<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace IpUpdate\Gui;

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