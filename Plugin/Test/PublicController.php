<?php


namespace Plugin\Test;


class PublicController extends \Ip\Controller
{
    public function testHmvc()
    {
        $response = new \Ip\Response();
        $response->setContent('TEST');
        return $response;
    }

    public function returnString()
    {
        return 'Some kind of a string';
    }

}
