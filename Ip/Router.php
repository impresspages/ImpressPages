<?php


namespace Ip;


/**
 * Minimal router compatible with the Aura Router 2 path syntax ImpressPages used:
 * required params `{name}`, optional segments `{/a,b}`, regex tokens via `where`,
 * GET/POST restriction via `method`, extra values and named URL generation.
 */
class Router
{
    /**
     * @var array name => route definition
     */
    protected $routes = array();

    protected $context;

    protected $routeIndex = 0;

    public function addRoutes($routes, $context = null)
    {
        foreach ($routes as $routeKey => $info) {

            $data = $this->routeData($routeKey, $info, $context);

            $route = array(
                'path' => $data['path'],
                'method' => null,
                'tokens' => array(),
                'values' => array(),
            );

            if (!empty($data['method']) && in_array($data['method'], array('GET', 'POST'))) {
                $route['method'] = $data['method'];
            }
            unset($data['path']);
            if (!empty($data['where'])) {
                $route['tokens'] = $data['where'];
                unset($data['where']);
            }

            $route['values'] = $data;
            $route['regex'] = $this->compile($route['path'], $route['tokens']);

            $this->routes[$data['name']] = $route;
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

    /**
     * Turn a route path into an anchored regex with named groups.
     */
    protected function compile($path, $tokens)
    {
        $regex = '';
        $offset = 0;
        preg_match_all('#\{(/?)([^}]+)\}#', $path, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ($matches as $match) {
            $regex .= preg_quote(substr($path, $offset, $match[0][1] - $offset), '#');
            $offset = $match[0][1] + strlen($match[0][0]);

            if ($match[1][0] === '/') {
                // Optional segments: {/a,b} => (?:/(?P<a>..)(?:/(?P<b>..))?)?
                $names = explode(',', $match[2][0]);
                $optional = '';
                foreach (array_reverse($names) as $name) {
                    $optional = '(?:/(?P<' . $name . '>' . $this->tokenRegex($name, $tokens) . ')' . $optional . ')?';
                }
                $regex .= $optional;
            } else {
                $name = $match[2][0];
                $regex .= '(?P<' . $name . '>' . $this->tokenRegex($name, $tokens) . ')';
            }
        }
        $regex .= preg_quote(substr($path, $offset), '#');

        return '#^' . $regex . '$#';
    }

    protected function tokenRegex($name, $tokens)
    {
        return isset($tokens[$name]) ? $tokens[$name] : '[^/]+';
    }

    public function match($path, $request = null)
    {
        if (!$request) {
            $request = ipRequest();
        }

        $server = $request->getServer();
        $requestMethod = isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : null;

        foreach ($this->routes as $route) {
            if ($route['method'] && $route['method'] !== $requestMethod) {
                continue;
            }
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $result = $route['values'];
            foreach ($matches as $key => $value) {
                if (is_string($key) && $value !== '') {
                    $result[$key] = rawurldecode($value);
                }
            }

            return $result;
        }

        return array();
    }

    public function generate($name, $data = array())
    {
        if (!isset($this->routes[$name])) {
            return false;
        }
        $route = $this->routes[$name];
        $data = array_merge($route['values'], $data);

        return preg_replace_callback(
            '#\{(/?)([^}]+)\}#',
            function ($match) use ($data) {
                if ($match[1] === '/') {
                    $url = '';
                    foreach (explode(',', $match[2]) as $name) {
                        if (!isset($data[$name]) || $data[$name] === '') {
                            break;
                        }
                        $url .= '/' . rawurlencode($data[$name]);
                    }
                    return $url;
                }
                return isset($data[$match[2]]) ? rawurlencode($data[$match[2]]) : '';
            },
            $route['path']
        );
    }
}
