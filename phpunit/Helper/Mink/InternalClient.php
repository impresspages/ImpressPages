<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper\Mink;

use Symfony\Component\BrowserKit\Client as BaseClient;
use Symfony\Component\BrowserKit\Response;

class InternalClient extends BaseClient
{
    /**
     * @param \Symfony\Component\BrowserKit\Request $request
     * @return Response
     * @throws \Exception
     * @throws CurlException
     */
    protected function doRequest($request)
    {
        $response = new Response();
        return $response;
    }
} 