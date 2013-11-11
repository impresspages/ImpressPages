<?php

namespace PhpUnit\Tests\Module\Install\Functional;

use \PhpUnit\Helper\TestEnvironment;

class SeleniumInstallTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::initCode();
        TestEnvironment::cleanupFiles();

        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->putInstallationFiles(TEST_TMP_DIR . 'installTest/');
    }

    /**
     * @return \Behat\Mink\Session
     */
    protected function getSession()
    {
        $driver = new \Behat\Mink\Driver\Selenium2Driver(
            'firefox', TEST_TMP_DIR
        );

        $session = new \Behat\Mink\Session($driver);

        $session->start();

        return $session;
    }

    public function testFullWorkflow()
    {
        $session = $this->getSession();

        $session->visit(TEST_TMP_URL . 'installTest/install/');

        $page = $session->getPage();
        $this->assertNotEmpty($page);

        $title = $page->find('css', 'title');
        $this->assertNotEmpty($title);
        $this->assertEquals('ImpressPages CMS installation wizard', $title->getHtml());
    }
}