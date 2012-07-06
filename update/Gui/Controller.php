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
    
    public function __construct()
    {
    }

    /**
     * @param \IpUpdate\Gui\View $view
     */
    public function setView(\IpUpdate\Gui\View $view)
    {
    }
    
    public function output()
    {
        echo $this->view->render();
    }
    
}