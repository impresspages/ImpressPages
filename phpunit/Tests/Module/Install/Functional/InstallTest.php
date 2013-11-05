<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Module\Install\Functional;

use \PhpUnit\Helper\TestEnvironment;

class InstallTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepare()
    {

    }

    public function testThis()
    {
        $this->markTestSkipped();

        return;

        TestEnvironment::initCode();

        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();


        $driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);
        $session->start();

        $session->visit(TEST_TMP_URL . 'install/');

        $page = $session->getPage();

        $this->assertEquals('ImpressPages CMS installation wizard', $page->find('css', 'title')->getText());
    }
}