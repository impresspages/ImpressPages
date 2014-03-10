<?php


namespace Ip;


class Router
{
    /**
     * @var \Aura\Router\RouteCollection
     */
    protected $auraRouter;

    protected $context;

    public function __construct()
    {
        $factory = new \Aura\Router\RouterFactory();
        $this->auraRouter = $factory->newInstance();
    }

    public function get($path, $name, $action = null)
    {
        $route = $this->auraRouter->add($name, $path);
        $route->addValues(array(
            'action' => $action ? $action : $name,
        ));
    }

    public function group($context, $callable)
    {
        $this->auraRouter->setValues($context);

        call_user_func($callable, $this);

        $this->auraRouter->setValues(array());
    }

    public function match($path, $request = null)
    {
        $result = $this->auraRouter->match($path);

        if (!$result) {
            return array();
        }

        $result = $result->params;

        if (is_callable($result['action'])) {
            return $result;
        }

        if (strpos($result['action'], '.')) {
            $tmp = explode('.', $result['action']);
            $result['plugin'] = $tmp[0];
            $result['action'] = $tmp[1];
        }

        return $result;
    }
}
