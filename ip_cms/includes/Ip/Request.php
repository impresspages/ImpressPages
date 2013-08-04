<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;


/**
 *
 * Class to get information about current request
 *
 */
class Request{

    protected $_SERVER = array();
    protected $_POST = array();
    protected $_GET = array();
    protected $_REQUEST = array();


    public function __construct()
    {
        $this->_SERVER = $_SERVER;
        $this->_POST = $_POST;
        $this->_GET = $_GET;
        $this->_REQUEST = $_REQUEST;
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
     * get request method  'GET', 'HEAD', 'POST', 'PUT'.
     * @return string
     */
    public function getMethod()
    {
        return $this->_SERVER['REQUEST_METHOD'];
    }


    public function paramsGet($name = null, $default = null)
    {
        return $this->getParam($name, $this->_GET, $default);
    }

    public function paramsPost($name = null, $default = null)
    {
        return $this->getParam($name, $this->_POST, $default);
    }

    public function paramsRequest($name = null, $default = null)
    {
        return $this->getParam($name, $this->_REQUEST, $default);
    }


    protected function getParam($name, $values, $default)
    {
        if ($name === null) {
            return $values;
        }
        if (!isset($values[$name])) {
            return $default;
        }
        return $values[$name];
    }

}