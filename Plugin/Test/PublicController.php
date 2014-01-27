<?php


namespace Plugin\Test;


class PublicController extends \Ip\Controller
{
    public function hello()
    {
        $page = new \Ip\Page(1, 'Test');
        $page->setPageTitle('Hello world');
        $page->setNavigationTitle('Hello world');

        $req = new \Ip\Request;
        echo 'xxx'.$req->getControllerType();

        _ipPageStart($page);

        return 'Hello world!';
    }

    public function testHmvc()
    {
        $response = new \Ip\Response();
        $response->setContent('TEST');
        return $response;
    }
}
