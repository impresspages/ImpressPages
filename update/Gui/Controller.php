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
    public $view;
    private $request;
    
    public function __construct(\IpUpdate\Gui\Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param \IpUpdate\Gui\View $view
     */
    public function setView(\IpUpdate\Gui\View $view)
    {
        $this->view = $view;
    }
    
    public function getOutput()
    {
        return $this->view->render();
    }
    
}