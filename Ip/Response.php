<?php
/**
 * @package   ImpressPages
 */

namespace Ip;


class Response
{
    static $headers = array();
    static $status = 200;

    public static function reset()
    {
        self::$headers = array();
    }

    public static function redirect($url)
    {
        self::header('Location: ' . $url);
        self::status(302);
    }

    public static function pageNotFound()
    {
        self::status(404);
        self::header('HTTP/1.0 404 Not Found');
    }

    public static function header($header)
    {
        self::$headers[]= $header;
    }

    public static function headers()
    {
        return self::$headers;
    }

    public static function status($code = null)
    {
        if ($code) {
            self::$status = $code;
        }

        return self::$status;
    }
}