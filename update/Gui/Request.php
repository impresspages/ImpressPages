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
        $controllerPath = $this->getCurrentControllerPath();
        $controllerClass = 'IpUpdate\\Gui\\Controller\\'.$controllerPath;
        
        $controller = new $controllerClass();
        
        return $controller;
        
        $controller = $this->getCurrentController();
        $action = $this->getCurrentAction();
        
        $actionMethod = $action.'Action';
        if (!method_exists($controller, $actionMethod) || !is_callable(array($controller, $actionMethod))) {
            throw new \IpUpdate\Gui\Exception('Requested action does not exist');
        } 
        
        $view = new \IpUpdate\Gui\View($controllerPath.'/'.$action.'.php');
        $controller->setView($controllerPath.'/'.$action.'.php');
        $controller->$action();
        $this->output = $controller->getOutput();
    }
    
    public function sendOutput()
    {
        $view = View::create('Layout/main.php', array('content' => $this->output));
        $output = $view->render();
        echo $output;
    }
        
    
    private function getCurrentControllerPath()
    {
        //default controller;
        $path = 'Overview';
        
        
        if (isset($_GET['controller'])) {
            switch (strtolower($_GET['controller'])) {
                default :
                case 'overview':
                    $path = 'Overview';
                    break;
                case 'update':
                    $path = 'Update';
                    break;
            }
        }
        return $path;
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