<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace IpUpdate\Gui;
        

class Request
{
    private static $instance;
    private $output;
    private $layout;

    public function __construct()
    {
        $this->setLayout(IUG_DEFAULT_LAYOUT);
    }
    
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
        
        $controller = new $controllerClass($this);

        $action = $this->getCurrentAction();
        
        $actionMethod = $action.'Action';
        if (!method_exists($controller, $actionMethod) || !is_callable(array($controller, $actionMethod))) {
            throw new \IpUpdate\Gui\Exception('Requested action ('.$actionMethod.') does not exist in controller '.$controllerClass.'');
        }
        
        if (file_exists(IUG_BASE_DIR.IUG_VIEW_DIR.$controllerPath.'/'.$action.'.php')) {
            $view = \IpUpdate\Gui\View::create('View/'.$controllerPath.'/'.$action.'.php');
            $controller->setView($view);
        }
        $controller->$actionMethod();
        
        $this->output = $controller->getOutput();
    }
    
    public function sendOutput()
    {
        if ($this->layout) {
            $view = View::create(IUG_LAYOUT_DIR.IUG_DEFAULT_LAYOUT, array('content' => $this->output));
            $output = $view->render();
        } else {
            $output = $this->output;
        }
        echo $output;
    }
    
    
    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        if ($layout) {
            $this->layout = IUG_LAYOUT_DIR.$layout;
        } else {
            $this->layout = null;
        }
    }
    
        
    
    private function getCurrentControllerPath()
    {
        //default controller;
        $path = 'Overview';
        
        
        if (isset($_GET['controller'])) {
            switch ($_GET['controller']) {
                default :
                case 'Overview':
                    $path = 'Overview';
                    break;
                case 'Update':
                    $path = 'Update';
                    break;
            }
        }
        return $path;
    }
    
    private function getCurrentAction() 
    {
        //default controller;
        $action = 'index';
        if (isset($_GET['action']) && preg_match('/^[A-Za-z_\-0-9]+$/', $_REQUEST['action'])) {
            $action = $_GET['action'];
        }
        return $action;
    }
   
}