<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Smoke;

use PhpUnit\Helper\TestEnvironment;

class InstallTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::cleanupFiles();
        TestEnvironment::initCode('install.php');
    }

    /**
     * @group Smoke
     */
    public function testInstall()
    {
        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();

        // init Mink:
        $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);
        $session->start();

        $installationUrl = $installation->getInstallationUrl();

        $session->visit($installationUrl);

        // get the current page URL:
        $this->assertEquals($installationUrl, $session->getCurrentUrl());

        $page = $session->getPage();

        $homepageTitle = $page->find('css', 'title');
        $this->assertNotEmpty($homepageTitle, 'Homepage rendering is broken!');
        $this->assertEquals('Home', $homepageTitle->getText());

        $headlineElement = $page->find('css', '.logo a');
        $this->assertNotEmpty($headlineElement, 'Headline is not visible!');
        $this->assertEquals('TestSite', $headlineElement->getText());
    }

}