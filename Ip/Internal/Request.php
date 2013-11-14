<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal;

//TODOX move outside Internal
/**
 *
 * Class to get information about current request
 *
 */
class Request
{
    protected $_SERVER = array();
    protected $_POST = array();
    protected $_GET = array();
    protected $_REQUEST = array();
    protected $currentLanguage;



    protected $languageUrl;
    protected $urlVars;
    protected $zoneUrl;


    public function __construct()
    {
        $this->_SERVER = $_SERVER;
        $this->_POST = $_POST;
        $this->_GET = $_GET;
        $this->_REQUEST = $_REQUEST;
//        $this->parseUrl();
    }

    public function setPost($post)
    {
        $this->_POST = $post;
        $this->_REQUEST = array_merge($this->_REQUEST, $post);
    }

    public function setServer($server)
    {
        $this->_SERVER = $server;
    }

    public function setGet($get)
    {
        $this->_GET = $get;
        $this->_REQUEST = array_merge($this->_REQUEST, $get);
    }

    public function setRequest($request)
    {
        $this->_REQUEST = $request;
    }



    public function getCurrentLanguage()
    {
        if ($this->currentLanguage === null) {

            //TODOX
        }
        return $this->currentLanguage;
    }





    public function isGet()
    {
        return $this->getMethod() == 'GET';
    }

    public function isPost()
    {
        return $this->getMethod() == 'POST';
    }

    /**
     * @throws \Ip\CoreException
     */
    public function mustBePost()
    {
        if (!$this->isPost()) {
            throw new \Ip\CoreException('POST method required.');
        }
    }

    /**
     * get request method  'GET', 'HEAD', 'POST', 'PUT'.
     * @return string
     */
    public function getMethod()
    {
        return $this->_SERVER['REQUEST_METHOD'];
    }


    /**
     * Returns GET query parameter if $name is passed. Returns all query parameters if name == null.
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
     * Returns request parameter if $name is passed. Returns all request parameters if name == null.
     *
     * @param string    $name       query parameter name
     * @param mixed     $default    default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public function getRequest($name = null, $default = null)
    {
        return $this->getParam($name, $this->_REQUEST, $default);
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
     * @return string path after BASE_URL
     */
    public function getRelativePath()
    {
        $basePath = parse_url(\Ip\Config::baseUrl(''), PHP_URL_PATH);

        if (strpos($this->_SERVER["REQUEST_URI"], $basePath) !== 0) {
            if ($this->_SERVER["REQUEST_URI"] == rtrim($basePath, '/')) {
                return '';
            }
            // TODO log error
            return $this->_SERVER["REQUEST_URI"];
        }

        return substr($this->_SERVER['REQUEST_URI'], strlen($basePath));
    }

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

    public function getZoneUrl()
    {
        return $this->zoneUrl;
    }

    public function getLanguageUrl()
    {
        return $this->languageUrl;
    }

    public function getUrlVars()
    {
        if ($this->urlVars === null) {
            $this->parseUrl();
        }
        return $this->urlVars;
    }

    private function parseUrl()
    {
        $this->parseUrl();
        $path = $this->getRelativePath();
        $urlVars = explode('/', rtrim(parse_url($path, PHP_URL_PATH), '/'));
        $this->urlVars = $urlVars;
        for ($i=0; $i< sizeof($urlVars); $i++){
            $urlVars[$i] = urldecode($urlVars[$i]);
        }
        if (ipGetOption('Config.multilingual')) {
            $this->languageUrl = urldecode(array_shift($urlVars));
        }

        $this->zoneUrl = urldecode(array_shift($urlVars));
        $this->urlVars = $urlVars;
    }
}