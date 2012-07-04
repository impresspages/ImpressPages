<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Gui;
        

class Request
{
    private static $instance;
    private $output;
    
    public static function getInstance() 
    { 
        if (!self::$instance) { 
            self::$instance = new Request(); 
        } 
        return self::$instance; 
    }
    
    public function execute()
    {
        $controller = $this->getCurrentController();
        $action = $this->getCurrentAction();
        
        $controller->$action();
        $this->output = $controller->getOutput();
    }
    
    public function sendOutput()
    {
        $view = View::create('Layout/main.php', array('content' => $this->output));
        $output = $view->render();
        echo $output;
    }
        
    
    private function getCurrentController()
    {
        //default controller;
        $controllerClass = 'IpUpdate\Gui\Controller\Overview';
        if (isset($_GET['controller'])) {
            switch (strtolower($_GET['controller'])) {
                default :
                case 'overview':
                    $controllerClass = 'IpUpdate\Gui\Controller\Overview';
                    break;
                case 'update':
                    $controllerClass = 'Ip\UpdateGui\Controller\Update';
                    break;
            }
        }
        
        //if (file_exists())
        
        $controller = new $controllerClass();
        
        return $controller;
    }
    
    private function getCurrentAction() 
    {
        //default controller;
        $action = 'indexAction';
        if (isset($_GET['action']) && preg_match('/^[A-Za-z_\-0-9]+$/', $_REQUEST['action'])) {
            $action = $_GET['action'].'Action';
        }
        return $action;
    }
}