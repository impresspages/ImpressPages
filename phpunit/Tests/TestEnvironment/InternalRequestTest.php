<?php
/**
 * @package   ImpressPages
 */

namespace Tests\InternalRequest;


class InternalRequestTest extends \PhpUnit\GeneralTestCase
{
    public function setup()
    {

//        parent::setup();
//        $config = include __DIR__ . '/ipConfig-default.php';
//        ipConfig()->init($config);
    }

//    public function testRenderHomepage()
//    {
//        \PhpUnit\Helper\TestEnvironment::cleanupFiles();
//        \PhpUnit\Helper\TestEnvironment::prepareFiles();
//        \PhpUnit\Helper\TestEnvironment::initCode();
//
//        $application = new \Ip\Application();
//        $response = $application->handleRequest();
//        $this->assertNotEmpty($response);
//    }
//
//    public function testUseMinkToRenderHomepage()
//    {
//        \PhpUnit\Helper\TestEnvironment::cleanupFiles();
//        \PhpUnit\Helper\TestEnvironment::prepareFiles();
//        \PhpUnit\Helper\TestEnvironment::initCode();
//
//        // init Mink:
//        $driver = new \PhpUnit\Helper\Mink\InternalDriver();
//        $session = new \Behat\Mink\Session($driver);
//        $session->start();
//
//        $session->visit(baseUrl);
//
//        // get the current page URL:
//        $this->assertEquals(baseUrl, $session->getCurrentUrl());
//
//        $page = $session->getPage();
//
//        $homepageTitle = $page->find('css', 'title');
//        $this->assertNotEmpty($homepageTitle, 'Homepage rendering is broken!');
//        $this->assertEquals('Home', $homepageTitle->getText());
//
//        $headlineElement = $page->find('css', 'p.homeHeadline');
//        $this->assertNotEmpty($headlineElement, 'Headline is not visible!');
//        $this->assertEquals('ImpressPages theme Blank', $headlineElement->getText());
//    }

    public function testLanguages()
    {
        \PhpUnit\Helper\TestEnvironment::setupCode();
        $languages = \Ip\Internal\ContentDb::getLanguages(true);
        $this->assertNotEmpty($languages);
        $language = array_shift($languages);
        $this->assertEquals('en', $language['url']);
    }

    public function testUseMinkToRenderEnVersion()
    {
        \PhpUnit\Helper\TestEnvironment::setup();

        // init Mink:
        $driver = new \PhpUnit\Helper\Mink\InternalDriver();
        // $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);
        $session->start();

        $session->visit(ipConfig()->baseUrl() . 'someNonExistentPage');

        $this->assertEquals(404, $session->getStatusCode());

        \PhpUnit\Helper\TestEnvironment::setupCode();
        $session->visit(ipConfig()->baseUrl());

        $page = $session->getPage();

        // $this->assertEquals('DEBUG', $page->getContent());


        $headlineElement = $page->find('css', '.logo a');
        $this->assertNotEmpty($headlineElement, 'Title is not visible!');
        $this->assertEquals('TestSite', $headlineElement->getText());

        $homepageTitle = $page->find('css', 'title');
        $this->assertNotEmpty($homepageTitle, 'Homepage rendering is broken!');
        $this->assertEquals('Home', $homepageTitle->getText());
    }

}
