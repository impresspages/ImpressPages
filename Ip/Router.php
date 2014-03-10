<?php


namespace Ip;


class Router
{
    /**
     * @var \Aura\Router\RouteCollection
     */
    protected $auraRouter;

    public function __construct()
    {
        $factory = new \Aura\Router\RouterFactory();
        $this->auraRouter = $factory->newInstance();
    }

    public function get($path, $name, $action = null)
    {
        $this->auraRouter->add($name, $path)->addValues(array(
            'action' => $action,
        ));
    }

} 
