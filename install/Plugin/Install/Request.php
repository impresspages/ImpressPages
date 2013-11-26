<?php
namespace Plugin\Install;

class Request extends \Ip\Request {
    protected $defaultControllerAction = 'index';
    protected $defaultControllerClass = '\\Plugin\\Install\\PublicController';

    protected function parseControllerAction()
    {
        $action = $this->defaultControllerAction;
        $controllerClass = $this->defaultControllerClass;
        $controllerType = self::CONTROLLER_TYPE_PUBLIC;


        if (isset($this->_REQUEST['aa']) || isset($this->_REQUEST['sa']) || isset($this->_REQUEST['pa'])) {
            throw new \Ip\CoreException('Controller action can be requested only at website root.');
        }
        $this->controllerClass = $controllerClass;
        $this->controllerAction = $action;
        $this->controllerType = $controllerType;
        return; //default controller to display page content.
    }
}