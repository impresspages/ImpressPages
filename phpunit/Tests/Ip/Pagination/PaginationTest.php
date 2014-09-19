<?php

namespace Tests\Ip\Pagination;

class PaginationTest extends \PhpUnit\GeneralTestCase
{
    public function setup()
    {
        \PhpUnit\Helper\TestEnvironment::setupCode();
    }

    public function testPagerSize5()
    {
        $pagination = new \Ip\Pagination\Pagination(array(
            'pagerSize' => 3,
            'totalPages' => 10,
            'currentPage' => 10,
        ));

        $expected = array(1, array('text' => '..', 'page' => 5), 10);

        $this->assertEquals($expected, $pagination->pages());

    }
}
