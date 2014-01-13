<?php


namespace Plugin\Test;


class PublicController extends \Ip\Controller
{
    public function hello()
    {
        ipCurrentPage()->_set('page', new \Ip\Page(1, 'Test'));

        return 'Hello world!';
    }
} 