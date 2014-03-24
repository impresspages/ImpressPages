<?php


namespace Plugin\Application;


class Filter
{
    /**
     * @param \Ip\Response $response
     * @return mixed
     */
    public static function ipSendResponse($response)
    {
        // modify response before sending
        return $response;
    }
}
