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

    public function __construct()
    {
        $this->_SERVER = $_SERVER;
    }

    public function isPost()
    {
        return $this->getMethod() == 'POST';
    }

    /**
     * get request method  'GET', 'HEAD', 'POST', 'PUT'.
     * @return string
     */
    public function getMethod() {
        return $this->_SERVER['REQUEST_METHOD'];
    }
}