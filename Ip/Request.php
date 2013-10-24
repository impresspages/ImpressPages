<?php
/**
 * @package   ImpressPages
 */

namespace Ip;


class Request {

    public static function isGet()
    {
        return \Ip\ServiceLocator::getRequest()->isGet();
    }

    public static function isPost()
    {
        return \Ip\ServiceLocator::getRequest()->isPost();
    }

    /**
     * @throws \Ip\CoreException
     */
    public static function mustBePost()
    {
        \Ip\ServiceLocator::getRequest()->mustBePost();
    }

    /**
     * get request method  'GET', 'HEAD', 'POST', 'PUT'.
     * @return string
     */
    public static function getMethod()
    {
        \Ip\ServiceLocator::getRequest()->getMethod();
    }

    /**
     * Returns GET query parameter if $name is passed. Returns all query parameters if name == null.
     *
     * @param string    $name       query parameter name
     * @param mixed     $default    default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public static function getQuery($name = null, $default = null)
    {
        return \Ip\ServiceLocator::getRequest()->getQuery($name, $default);
    }

    /**
     * Returns POST parameter if $name is passed. Returns all query parameters if name == null.
     *
     * @param string    $name       query parameter name
     * @param mixed     $default    default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public static function getPost($name = null, $default = null)
    {
        return \Ip\ServiceLocator::getRequest()->getPost($name, $default);
    }

    /**
     * Returns request parameter if $name is passed. Returns all request parameters if name == null.
     *
     * @param string    $name       query parameter name
     * @param mixed     $default    default value if no GET parameter exists
     * @return mixed    GET query variable (all query variables if $name == null)
     */
    public static function getRequest($name = null, $default = null)
    {
        return \Ip\ServiceLocator::getRequest()->getRequest($name, $default);
    }

    public static function getUrl()
    {
        return \Ip\ServiceLocator::getRequest()->getUrl();
    }

    /**
     * @return string path after BASE_URL
     */
    public static function getRelativePath()
    {
        return \Ip\ServiceLocator::getRequest()->getRelativePath();
    }
}