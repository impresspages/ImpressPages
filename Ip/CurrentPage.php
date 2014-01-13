<?php


namespace Ip;


class CurrentPage
{
    private $requestedPage;

    public function __construct($requestedPage)
    {
        $this->requestedPage = $requestedPage;
    }

    /**
     * @return \Ip\Language
     */
    public function getLanguage()
    {
        return $this->requestedPage['language'];
    }

    public function getControllerClass()
    {
        return $this->requestedPage['controllerClass'];
    }

    public function getControllerType()
    {
        return $this->requestedPage['controllerType'];
    }

    public function getControllerModule()
    {
        return $this->requestedPage['controllerModule'];
    }

    public function getControllerAction()
    {
        return $this->requestedPage['controllerAction'];
    }

    public function getPage()
    {
        return isset($this->requestedPage['page']) ? $this->requestedPage['page'] : NULL;
    }

    public function getUrlPath()
    {
        return !empty($this->requestedPage['urlVars']) ? $this->requestedPage['urlVars'] : array();
    }

    public function getZone()
    {
        if (!isset($this->requestedPage['zone'])) {
            return null;
        }

        return ipContent()->getZone($this->requestedPage['zone']);
    }

    public function getCurrentRevision()
    {
        return null; // TODOX get revision
    }

    public function getType()
    {
        return $this->requestedPage['controllerType'];
    }

    public function _set($name, $value)
    {
        $this->requestedPage[$name] = $value;
    }

    public function get($name, $default = NULL)
    {
        return array_key_exists($name, $this->requestedPage) ? $this->requestedPage[$name] : $default;
    }
}