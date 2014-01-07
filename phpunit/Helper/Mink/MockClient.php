<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper\Mink;

use Symfony\Component\BrowserKit\Response;

class MockClient extends InternalClient
{
    var $response = null;

    public function setResponse($html, $status = 200, $headers = array())
    {
        $this->response = new Response($html, $status, $headers);
    }

    /**
     * @param \Symfony\Component\BrowserKit\Request $request
     * @return Response
     * @throws \Exception
     * @throws CurlException
     */
    protected function doRequest($request)
    {
        if (!$this->response) {
            throw new \Ip\Exception('Response is not prepared');
        }

        $response = $this->response;
        $this->response = null;

        return $response;
    }
} 