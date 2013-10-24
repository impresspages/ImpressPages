<?php
/**
 * @package   ImpressPages
 */

namespace Tests\InternalRequest;


class ExperimentTest extends \PhpUnit\GeneralTestCase
{
    public function setup()
    {
//        parent::setup();
//        $config = include __DIR__ . '/ipConfig-default.php';
//        \Ip\Config::init($config);
    }

    public function testRenderHomepage()
    {

    }

//    public function testRenderHomepage()
//    {
//        require_once TEST_BASE_DIR . 'vendor/mink.phar';
//
//        \PhpUnit\Helper\TestEnvironment::cleanupFiles();
//        \PhpUnit\Helper\TestEnvironment::prepareFiles();
//        \PhpUnit\Helper\TestEnvironment::initCode();
//
//        // init Mink:
//        $driver = new \PhpUnit\Helper\Mink\InternalDriver();
//        $session = new \Behat\Mink\Session($driver);
//        $session->start();
//
//        $session->visit(BASE_URL);
//
//        // get the current page URL:
//        $this->assertEquals($installationUrl, $session->getCurrentUrl());
//
//        $page = $session->getPage();
//
//        $this->assertEquals('DEBUG', $page->getContent());
//
//        $homepageTitle = $page->find('css', 'title');
//        $this->assertNotEmpty($homepageTitle, 'Homepage rendering is broken!');
//        $this->assertEquals('Home', $homepageTitle->getText());
//
//        $headlineElement = $page->find('css', 'p.homeHeadline');
//        $this->assertNotEmpty($headlineElement, 'Headline is not visible!');
//        $this->assertEquals('ImpressPages theme Blank', $headlineElement->getText());
//
//
//    }
}