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
        global $application;

        $serverInfo = $request->getServer();

        $server = array(
            'REQUEST_URI' => parse_url($request->getUri(), PHP_URL_PATH),
            'REQUEST_METHOD' => $request->getMethod(),
            'SERVER_PORT' => 80,
            'SERVER_NAME' => $serverInfo['HTTP_HOST'],
        );

        $ipRequest = new \Ip\Request();
        $ipRequest->setServer($server);

        if ($request->getMethod() == 'GET') {
            $ipRequest->setQuery($request->getParameters());
        } elseif ($request->getMethod() == 'POST') {
            $ipRequest->setPost($request->getParameters());
        }

        $application = new \Ip\Application(NULL);
        $ipResponse = $application->handleRequest($ipRequest, array(), false);

        $response = new Response($ipResponse->render(), $ipResponse->getStatusCode(), $ipResponse->getHeaders());

        return $response;
    }
}
