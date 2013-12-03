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

        parent::parseControllerAction();
        if ($this->controllerClass !== 'Plugin\Install\PublicController') {
            $this->controllerAction = $this->defaultControllerAction;
            $this->controllerClass = $this->defaultControllerClass;
            $this->controllerType = self::CONTROLLER_TYPE_PUBLIC;
        }


        return; //default controller to display page content.
    }
}