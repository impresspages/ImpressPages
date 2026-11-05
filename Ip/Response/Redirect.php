<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Response;

class Redirect extends \Ip\Response
{
    protected static $statusLines = array(
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
    );

    /**
     * @param string $url
     * @param string|null $content
     * @param array|null $headers
     * @param int $statusCode 301 (default), 302, 303, 307 or 308. Use 302/303 for anything that depends
     *   on the session or a form post: browsers cache 301 and 308 and may not ask the server again.
     */
    public function __construct($url, $content = null, $headers = null, $statusCode = 301)
    {
        $statusCode = isset(self::$statusLines[(int)$statusCode]) ? (int)$statusCode : 301;
        parent::__construct($content, $headers, $statusCode);
        $this->addHeader('HTTP/1.1 ' . $statusCode . ' ' . self::$statusLines[$statusCode]);
        $this->addHeader('Location: ' . $url);
    }

}
