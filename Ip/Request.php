<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

/**
 * Get current HTTP request information
 *
 */
class Request
{
    protected $_SERVER = array();
    protected $_POST = array();
    protected $_GET = array();
    protected $_REQUEST = array();
    protected $_COOKIE = array();


    /**
     * @var \Ip\controller
     */
    protected $controller = null;

    protected $routePath = null;


    public function __construct()
    {
        $server = $_SERVER;
        $server['REDIRECT_QUERY_STRING'] = '';
        $server['REDIRECT_URL'] = '';
        $server['QUERY_STRING'] = '';
        $server['REQUEST_URI'] = parse_url(ipConfig()->baseUrl(), PHP_URL_PATH); // default uri points to root
        $this->setServer($server);
    }

    /**
     * Set post variables
     * @param $post
     */
    public function setPost($post)
    {
        $this->_POST = $post;
        $this->_REQUEST = array_merge($this->_REQUEST, $post);
    }

    /**
     * Set server data
     * @param $server
     */
    public function setServer($server)
    {
        $this->_SERVER = $server;
    }

    /**
     * Set GET query
     * @param $query
     */
    public function setQuery($query)
    {
        $this->_GET = $query;
        $this->_REQUEST = array_merge($this->_REQUEST, $query);
    }

    /**
     * Set request data
     * @param $request
     */
    public function setRequest($request)
    {
        $this->_REQUEST = $request;
    }

    /**
     * Check if HTTP request data is provided using GET method
     *
     * @return bool Returns true for GET method
     */

    public function isGet()
    {
        return $this->getMethod() == 'GET';
    }

    /**
     * Check if HTTP request data is provided using POST method
     *
     * @return bool Returns true for POST method
     */
    public function isPost()
    {
        return $this->getMethod() == 'POST';
    }

    public function isAjax()
    {
        return strtolower($this->getServer('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest';
    }

    /**
     * Require to provide HTTP request data using POST method
     *
     * @throws \Ip\Exception is thrown if POST method was not used.
     */
    public function mustBePost()
    {
        if (!$this->isPost()) {
            throw new \Ip\Exception('POST method required.');
        }
    }

    /**
     * Check if HTTPS protocol is used
     * @return bool Returns true for HTTPS request
     */
    public function isHttps()
    {
        return (isset($this->_SERVER["HTTPS"]) && strtolower($this->_SERVER["HTTPS"]) == "on");
    }


    /**
     * Get request method, such as 'GET', 'HEAD', 'POST', or 'PUT'
     *
     * @return string Request method
     */
    public function getMethod()
    {
        return $this->_SERVER['REQUEST_METHOD'];
    }


    /**
     * Return GET query parameter if $name is passed. Returns all query parameters if name == null.
     *
     * @param string $name query parameter name
     * @param mixed $default default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public function getQuery($name = null, $default = null)
    {
        return $this->getParam($name, $this->_GET, $default);
    }

    /**
     * Returns POST parameter if $name is passed. Returns all query parameters if name == null.
     *
     * @param string $name query parameter name
     * @param mixed $default default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public function getPost($name = null, $default = null)
    {
        return $this->getParam($name, $this->_POST, $default);
    }

    /**
     * Return request parameter if $name is passed. Returns all request parameters if $name == null.
     *
     * @param string $name query parameter name
     * @param mixed $default default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public function getRequest($name = null, $default = null)
    {
        return $this->getParam($name, $this->_REQUEST, $default);
    }

    /**
     * Return parameters, such as headers, paths, and script locations, provided in $_SERVER array
     *
     * @param string $name parameter name
     * @param string $default default value returned when a server parameter is null
     * @return mixed
     */

    public function getServer($name = null, $default = null)
    {
        return $this->getParam($name, $this->_SERVER, $default);
    }

    protected function getParam($name, $values, $default)
    {
        if ($name === null) {
            return $values;
        }
        if (!array_key_exists($name, $values)) {
            return $default;
        }
        return $values[$name];
    }

    /**
     * Get current page URL
     *
     * @return string URL address
     */
    public function getUrl()
    {
        $pageURL = 'http';
        if (isset($this->_SERVER["HTTPS"]) && strtolower($this->_SERVER["HTTPS"]) == "on") {
            $pageURL .= "s";
        }
        $pageURL .= '://';
        if ($this->_SERVER["SERVER_PORT"] != "80" && $this->_SERVER["SERVER_PORT"] != "443") {
            $pageURL .= $this->_SERVER["SERVER_NAME"] . ":" . $this->_SERVER["SERVER_PORT"] . $this->_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $this->_SERVER["SERVER_NAME"] . $this->_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * Gets relative path from base URL
     *
     * @return string Path after BASE_URL
     */
    public function getRelativePath()
    {
        $basePath = parse_url(ipConfig()->baseUrl(), PHP_URL_PATH);

        $requestPath = parse_url($this->_SERVER["REQUEST_URI"], PHP_URL_PATH);

        if (strpos($requestPath, $basePath) !== 0) {
            if ($requestPath == rtrim($basePath, '/')) {
                return '';
            }
            // TODO log error
            return $requestPath;
        }

        $relativePath = substr($requestPath, strlen($basePath));

        if (strpos($relativePath, 'index.php') === 0) { // remove index.php if needed
            $relativePath = substr($relativePath, 9);
        }

        return $relativePath ? ltrim(urldecode($relativePath), '/') : '';
    }

    /**
     * @private
     * for internal ImpressPages uses only
     */
    public function _setRoutePath($routePath)
    {
        $this->routePath = $routePath;
    }

    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * @ignore
     * @return bool true if current url is pointing to website root or one of the languages
     */
    public function _isWebsiteRoot()
    {
        $relativePath = $this->getRelativePath();

        if (!$relativePath || (empty($relativePath[0]) || $relativePath[0] == '?' || $relativePath == 'index.php')) {
            return true;
        }

        return false;
    }


}
