<?php


namespace Ip;


class Router
{
    /**
     * @var \Aura\Router\RouteCollection
     */
    protected $auraRouter;

    protected $context;

    protected $routeIndex = 0;

    public function __construct()
    {
        $factory = new \Aura\Router\RouterFactory();
        $this->auraRouter = $factory->newInstance();
    }

    public function addRoutes($routes, $context = null)
    {
        foreach ($routes as $routeKey => $info) {

            $data = $this->routeData($routeKey, $info, $context);
            $route = $this->auraRouter->add($data['name'], $data['path']);
            if (!empty($data['method']) && in_array($data['method'], array('GET', 'POST'))) {
                $route->addServer(array('REQUEST_METHOD' => $data['method']));
            }
//            unset($data['name']);
            unset($data['path']);
            if (!empty($data['where'])) {
                $route->addTokens($data['where']);
                unset($data['where']);
            }

            $route->addValues($data);
        }
    }

    protected function routeData($key, $value, $context)
    {
        $data = $context;

        if (!is_numeric($key)) {
            $data['path'] = $key;
        } elseif (!empty($value['path'])) {
            $data['path'] = $value['path'];
        } else {
            throw new \Ip\Exception('Invalid route.');
        }

        if (is_string($value)) {
            $data['action'] = $value;
        } elseif (is_callable($value)) {
            $data['action'] = $value;
        } elseif (is_array($value)) {
            $data = array_merge($data, $value);
        }

        if (empty($data['name'])) {
            $data['name'] = '_route' . ++$this->routeIndex;
        }

        return $data;
    }

    public function match($path, $request = null)
    {
        if (!$request) {
            $request = ipRequest();
        }

        $result = $this->auraRouter->match($path, $request->getServer());

        if (!$result) {
            return array();
        }

        $result = $result->params;

        if (is_callable($result['action'])) {
            return $result;
        }

        return $result;
    }

    public function generate($name, $data = array())
    {
        return $this->auraRouter->generate($name, $data);
    }
}
