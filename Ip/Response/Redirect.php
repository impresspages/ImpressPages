<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Response;

class Redirect extends \Ip\Response
{

    public function __construct($url, $content = null, $headers = null, $statusCode = 301)
    {
        parent::__construct($content, $headers, $statusCode);
        $this->addHeader('HTTP/1.1 301 Moved Permanently');
        $this->addHeader('Location: ' . $url);
    }

}
