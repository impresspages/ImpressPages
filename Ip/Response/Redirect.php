<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Response;

class Redirect extends \Ip\Response {

    public function __construct($url, $content = NULL, $headers = NULL, $statusCode = NULL)
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: '.$url);
        parent::__construct($content, $headers, $statusCode);
    }

}