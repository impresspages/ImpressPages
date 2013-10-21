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

        $session->visit($installation->getInstallationUrl());

        // get the current page URL:
        $this->assertEquals($installation->getInstallationUrl(), $session->getCurrentUrl());

        $page = $session->getPage();

        $this->assertEquals('Home', $page->find('css', 'title')->getText());
        $this->assertEquals('ImpressPages theme Blank', $page->find('css', 'p.homeHeadline')->getText());
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