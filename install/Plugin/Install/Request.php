<?php
namespace Plugin\Install;

class Request extends \Ip\Request {
    protected $defaultControllerAction = 'index';
    protected $defaultControllerClass = '\\Plugin\\Install\\PublicController';


    //original function requires database access
    protected function isWebsiteRoot()
    {
        return true;
    }

    protected function parseControllerAction()
    {
        $action = $this->defaultControllerAction;
        $controllerClass = $this->defaultControllerClass;
        $controllerType = self::CONTROLLER_TYPE_PUBLIC;


        parent::parseControllerAction();

        if ($this->controllerClass !== 'Plugin\Install\PublicController') {
            $action = $this->defaultControllerAction;
            $controllerClass = $this->defaultControllerClass;
            $controllerType = self::CONTROLLER_TYPE_PUBLIC;
        }

        $this->controllerClass = $controllerClass;
        $this->controllerAction = $action;
        $this->controllerType = $controllerType;
        return; //default controller to display page content.
    }
}