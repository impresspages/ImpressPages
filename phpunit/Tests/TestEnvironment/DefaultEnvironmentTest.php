<?php
/**
 * @package   ImpressPages
 */

class DefaultEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testCleanTestEnvironment()
    {
        \PhpUnit\Helper\Cleanup::cleanupFiles();
    }

    public function testDefaultTestInstallation()
    {
        require_once TEST_BASE_DIR . 'vendor/mink.phar';

        \PhpUnit\Helper\Cleanup::cleanupFiles();
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

        $headlineElement = $page->find('css', 'p.homeHeadline');
        $this->assertNotEmpty($headlineElement, 'Headline is not visible!');
        $this->assertEquals('ImpressPages theme Blank', $headlineElement->getText());
    }

    /**
     *   Scenario: Loading default test environment
     *    Given I am in a test
     *    When I load test environment
     *    Then Default test constants should be set
     */
    public function testDefaultTestEnvironment()
    {
        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        \Ip\Config::init($config);

        $this->assertEquals(realpath(TEST_BASE_DIR . TEST_CODEBASE_DIR), \Ip\Config::getRaw('BASE_DIR'));
    }
}