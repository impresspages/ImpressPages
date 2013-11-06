<?php

namespace Tests\Module\Install\Functional;

use \PhpUnit\Helper\TestEnvironment;

class SeleniumInstallTest extends \PHPUnit_Framework_TestCase
{
    public function testFullWorkflow()
    {
        TestEnvironment::initCode();
        TestEnvironment::cleanupFiles();

        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->putInstallationFiles(TEST_TMP_DIR . 'installTest/');

        $driver = new \Behat\Mink\Driver\Selenium2Driver(
            'firefox', TEST_TMP_DIR
        );

        $session = new \Behat\Mink\Session($driver);
        /* @var $session \Behat\Mink\FakeSession */ // Hack for PhpStorm bug
        $session->start();

        $session->visit(TEST_TMP_URL . 'installTest/install/');

        $page = $session->getPage();

        $this->assertEquals('ImpressPages CMS installation wizard', $page->find('css', 'title')->getText());
    }
}