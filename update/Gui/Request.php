<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Gui;
        

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
        
        ob_start();
        $controller->$action();
        $this->output = ob_get_contents();
        ob_end_clean();
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
        $controllerClass = 'Gui\Controller\Backup';
        if (isset($_GET['controller'])) {
            switch (strtolower($_GET['controller'])) {
                default :
                case 'backup':
                    $controllerClass = 'Gui\Controller\Backup';
                    break;
                case 'update':
                    $controllerClass = 'Gui\Controller\Update';
                    break;
            }
        }
        
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