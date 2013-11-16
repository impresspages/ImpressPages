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
        $_GET = array();
        $_POST = array();
        $server = $request->getServer();
        $_SERVER = array(
            'REQUEST_URI' => parse_url($request->getUri(), PHP_URL_PATH),
            'REQUEST_METHOD' => $request->getMethod(),
            'SERVER_PORT' => 80,
            'SERVER_NAME' => $server['HTTP_HOST'],
        );

        if ($request->getMethod() == 'GET') {
            $_GET = $request->getParameters();
        } elseif ($request->getMethod() == 'POST') {
            $_POST = $request->getParameters();
        }

        \Ip\ServiceLocator::replaceRequestService(new \Ip\Internal\Request());
        //TODOX Application needs a config param
        $application = new \Ip\Core\Application();
        $ipResponse = $application->handleRequest();

        //TODOX ResponseSugar doesn't exist
        $response = new Response($ipResponse, \Ip\ResponseSugar::status(), \Ip\ResponseSugar::headers());

        return $response;
    }
} 