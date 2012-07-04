<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Gui;

class Controller
{
    private $content;
    private $view;
    
    public function __construct($defaultView)
    {
        $this->view = $defaultView;
    }
    
    public function setOutput($output)
    {
        $this->output = $output;
    }
    
    public function getOutput()
    {
        return $this->output;
    }
    
}