<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Module\Install\Functional;

use \PhpUnit\Helper\TestEnvironment;

class InstallTest extends \PHPUnit_Framework_TestCase
{
    public function testFullWorkflow()
    {
        TestEnvironment::initCode();
        TestEnvironment::cleanupFiles();

        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->putInstallationFiles(TEST_TMP_DIR . 'installTest/');

        $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);

        $session->start();

        $session->visit(TEST_TMP_URL . 'installTest/install/');

        $page = $session->getPage();
        $this->assertNotEmpty($page);

        $title = $page->find('css', 'title');
        $this->assertNotEmpty($title);
        $this->assertEquals('ImpressPages CMS installation wizard', $title->getText());
    }
}