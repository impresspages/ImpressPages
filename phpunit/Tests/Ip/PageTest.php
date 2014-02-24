<?php


namespace Tests\Ip;


use PhpUnit\Helper\TestEnvironment;

class PageTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }

    public function testLink()
    {
        $firstPageId = \Ip\Internal\Pages\Service::addPage(0, 'First page', array('languageCode' => 'en'));
        $this->assertNotEmpty($firstPageId);
        $page = \Ip\Internal\Pages\Service::getPage($firstPageId);
        $this->assertEquals('en', $page['languageCode']);

        $this->assertEquals('http://localhost/en/first-page', ipPage($firstPageId)->getLink());
    }
}
