<?php


namespace Plugin\Test;


class PublicController extends \Ip\Controller
{
    public function hello()
    {
        $page = new \Ip\Page(1, 'Test');
        $page->setPageTitle('Hello world');
        $page->setNavigationTitle('Hello world');

        _ipPageStart($page);

        return 'Hello world!';
    }
} 