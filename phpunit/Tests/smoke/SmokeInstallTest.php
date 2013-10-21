<?php
/**
 * @package   ImpressPages
 */

class SmokeInstallTest extends \PhpUnit\GeneralTestCase
{
    private $installation;

    public function testInstallScreen()
    {
        require_once TEST_BASE_DIR . 'vendor/mink.phar';

        // install fresh copy of ImpressPages:
        $installation = $this->getInstallation();

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

        $headlineElement = $page->find('css', 'p.homeHeadline');
        $this->assertNotEmpty($headlineElement, 'Headline is not visible!');
        $this->assertEquals('ImpressPages theme Blank', $headlineElement->getText());
    }

    /**
     * @return \PhpUnit\Helper\Installation
     */
    protected function getInstallation()
    {
        if (!$this->installation) {
            $installation = new \PhpUnit\Helper\Installation(); //development version
            $installation->install();
            $this->installation = $installation;
        }
        return $this->installation;
    }

}