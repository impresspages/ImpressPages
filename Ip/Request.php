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




    protected $controllerAction = null;
    protected $controllerClass = null;
    protected $controllerType = null;
    protected $controllerModule = null;
    protected $defaultControllerAction = 'index';
    protected $defaultControllerClass = '\\Ip\\Internal\\Content\\PublicController';
    protected $defaultControllerModule = 'Content';

    const CONTROLLER_TYPE_PUBLIC = 0;
    const CONTROLLER_TYPE_SITE = 1;
    const CONTROLLER_TYPE_ADMIN = 2;


    /**
     * @var \Ip\controller
     */
    protected $controller = null;


    public function __construct()
    {
        $server = $_SERVER;
        $server['REDIRECT_QUERY_STRING'] = '';
        $server['REDIRECT_URL'] = '';
        $server['QUERY_STRING'] = '';
        $server['REQUEST_URI'] = '/';
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
        return (isset($this->_SERVER["HTTPS"]) && $this->_SERVER["HTTPS"] == "on");
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
     * @param string    $name       query parameter name
     * @param mixed     $default    default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public function getQuery($name = null, $default = null)
    {
        return $this->getParam($name, $this->_GET, $default);
    }

    /**
     * Returns POST parameter if $name is passed. Returns all query parameters if name == null.
     *
     * @param string    $name       query parameter name
     * @param mixed     $default    default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public function getPost($name = null, $default = null)
    {
        return $this->getParam($name, $this->_POST, $default);
    }

    /**
     * Return request parameter if $name is passed. Returns all request parameters if $name == null.
     *
     * @param string    $name       query parameter name
     * @param mixed     $default    default value if no GET parameter exists
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
        if (!array_key_exists($name,  $values)) {
            return $default;
        }
        return $values[$name];
    }

    /**
     * Get current page URL
     *
     * @return string URL address
     */
    public function getUrl() {
        $pageURL = 'http';
        if (isset($this->_SERVER["HTTPS"]) && $this->_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= '://';
        if ($this->_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $this->_SERVER["SERVER_NAME"].":".$this->_SERVER["SERVER_PORT"].$this->_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $this->_SERVER["SERVER_NAME"].$this->_SERVER["REQUEST_URI"];
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

        if (strpos($requestPath, '/index.php') === 0) { // remove index.php if needed
            $requestPath = substr($requestPath, 10);
        }

        $relativePath = substr($requestPath, strlen($basePath));

        return $relativePath ? ltrim($relativePath, '/') : '';
    }

    /**
     * @ignore
     */
    public function fixMagicQuotes()
    {
        if (!get_magic_quotes_gpc()) {
            return;
        }

        $process = array(&$this->_GET, &$this->_POST, &$this->_COOKIE, &$this->_REQUEST);
        while (list($key, $val) = each($process)) {
            foreach ($val as $k => $v) {
                unset($process[$key][$k]);
                if (is_array($v)) {
                    $process[$key][stripslashes($k)] = $v;
                    $process[] = & $process[$key][stripslashes($k)];
                } else {
                    $process[$key][stripslashes($k)] = stripslashes($v);
                }
            }
        }
        unset($process);
    }

    /**
     * Gets MVC controller action
     *
     * @return string controller action name
     */
    public function getControllerAction()
    {
        if (!$this->controllerAction) {
            $this->parseControllerAction();
        }
        return $this->controllerAction;
    }

    /**
     * Gets MVC controller action class
     *
     * @return null
     */
    public function getControllerClass()
    {
        if (!$this->controllerClass) {
            $this->parseControllerAction();
        }
        return $this->controllerClass;
    }

    /**
     * @ignore
     * @return bool true if current url is pointing to website root or one of the languages
     */
    public function _isWebsiteRoot()
    {
        $relativePath = $this->getRelativePath();

        if (!$relativePath || !(ltrim($relativePath, '/')) || (empty($relativePath[0]) || $relativePath[0] == '?' || ltrim($relativePath, '/') == 'index.php')) {
            return true;
        }

        return false;
    }

    protected function parseControllerAction()
    {
        $action = $this->defaultControllerAction;
        $controllerClass = $this->defaultControllerClass;
        $controllerType = self::CONTROLLER_TYPE_PUBLIC;
        $controllerModule = $this->defaultControllerModule;

        if (!$this->_isWebsiteRoot()) {
            if (isset($this->_REQUEST['aa']) || isset($this->_REQUEST['sa']) || isset($this->_REQUEST['pa'])) {
                throw new \Ip\Exception('Controller action can be requested only at website root.');
            }
            $this->controllerClass = $controllerClass;
            $this->controllerAction = $action;
            $this->controllerType = $controllerType;
            return; //default controller to display page content.
        }

        if (sizeof($this->getRequest()) > 0) {
            $actionString = null;
            if(isset($this->_REQUEST['aa'])) {
                $actionString = $this->_REQUEST['aa'];
                $controllerClass = 'AdminController';
                $controllerType = self::CONTROLLER_TYPE_ADMIN;
            } elseif(isset($this->_REQUEST['sa'])) {
                $actionString = $this->_REQUEST['sa'];
                $controllerClass = 'SiteController';
                $controllerType = self::CONTROLLER_TYPE_SITE;
            } elseif(isset($this->_REQUEST['pa'])) {
                $actionString = $this->_REQUEST['pa'];
                $controllerClass = 'PublicController';
                $controllerType = self::CONTROLLER_TYPE_PUBLIC;
            }

            if ($actionString) {
                $parts = explode('.', $actionString);
                $controllerModule = array_shift($parts);
                if (isset($parts[0])) {
                    $action = $parts[0];
                }

                $controllerClass = $this->generateControllerClass($controllerModule, $controllerType);
            }

        }

        $this->controllerClass = $controllerClass;
        $this->controllerAction = $action;
        $this->controllerType = $controllerType;
        $this->controllerModule = $controllerModule;
    }

    /**
     * Get controller type: public, site or admin
     * @return string
     */
    public function getControllerType()
    {
        if ($this->controllerType === null) {
            $this->parseControllerAction();
        }
        return $this->controllerType;
    }

    /**
     * Set a controller action
     *
     * @param string $module controller module name
     * @param string $action controller action name
     * @param $type public, site or admin controller
     * @throws Exception
     */
    public function setAction($module, $action, $type)
    {
        if (!in_array($type, array (self::CONTROLLER_TYPE_ADMIN, self::CONTROLLER_TYPE_PUBLIC, self::CONTROLLER_TYPE_SITE))) {
            throw new \Ip\Exception("Incorrect controller type");
        }
        $this->controllerType = $type;
        $this->controller = null;
        $this->controllerClass = $this->generateControllerClass($module, $type);

        $this->controllerAction = $action;
    }

    private function generateControllerClass($module, $type)
    {
        switch ($type) {
            case self::CONTROLLER_TYPE_ADMIN:
                $className = 'AdminController';
                break;
            case self::CONTROLLER_TYPE_SITE:
                $className = 'SiteController';
                break;
            case self::CONTROLLER_TYPE_PUBLIC:
                $className = 'PublicController';
                break;
        }


        if (in_array($module, \Ip\Internal\Plugins\Model::getModules())) {
            $controllerClass = 'Ip\\Internal\\'.$module.'\\'.$className;
        } else {
            $controllerClass = 'Plugin\\'.$module.'\\'.$className;
        }
        return $controllerClass;
    }

    /**
     * Check if this is the default controller action
     * @return bool Returns true for default controller action
     */
    public function isDefaultAction()
    {
        return $this->getControllerClass() == $this->defaultControllerClass && $this->getControllerAction() == $this->defaultControllerAction;
    }


}
