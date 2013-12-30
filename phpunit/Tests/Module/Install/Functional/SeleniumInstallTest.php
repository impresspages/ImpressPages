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
        if (getenv('TRAVIS')) {

            // $url = sprintf('http://%s:%s@localhost:4445/wd/hub', getenv('SAUCE_USERNAME'), getenv('SAUCE_ACCESS_KEY'));
            $url = sprintf('http://%s:%s@ondemand.saucelabs.com/wd/hub', getenv('SAUCE_USERNAME'), getenv('SAUCE_ACCESS_KEY'));
            $desiredCapabilities = array(
                'name' => __METHOD__,
                'tunnel-identifier' => getenv('TRAVIS_JOB_NUMBER'),
                'build' => getenv('TRAVIS_BUILD_NUMBER'),
                'tags' => array(getenv('TRAVIS_PHP_VERSION'), 'CI')
            );

            $driver = new \Behat\Mink\Driver\Selenium2Driver(
                'firefox',
                $desiredCapabilities,
                $url
            );
        } else {
            $driver = new \Behat\Mink\Driver\Selenium2Driver(
                'firefox'
            );
        }

        try {
            $session = new \Behat\Mink\Session($driver);

            $session->start();
        } catch (\Behat\Mink\Exception\DriverException $e) {
            $this->markTestSkipped('Could not connect open Mink connection.');
        }

        return $session;
    }

    /**
     * @group Sauce
     */
    public function testInstallCurrent($customPort = NULL)
    {
        $session = $this->getSession();

        $session->visit(TEST_TMP_URL . 'installTest/install/');

        $page = $session->getPage();
        $this->assertNotEmpty($page, 'Page should not be empty');

        $title = $page->find('css', 'title');
        $this->assertNotEmpty($title, 'Title should not be empty');
        $this->assertEquals('ImpressPages CMS installation wizard', $title->getHtml());

        $page->find('css', '.button_act')->click();
        $this->assertEquals('System check', $page->find('css', 'h1')->getText());
        $this->assertNotContains('on line', $page->getContent());
        $this->assertFalse($page->has('css', '.error'));


        $page->find('css', '.button_act')->click();
        $this->assertEquals('ImpressPages Legal Notices', $page->find('css', 'h1')->getText());
        $this->assertNotContains('on line', $page->getContent());
        $this->assertFalse($page->has('css', '.error'));

        $page->find('css', '.button_act')->click();
        $this->assertEquals('Database installation', $page->find('css', 'h1')->getText());
        // There is a hidden error message


        $testDbHelper = new \PhpUnit\Helper\TestDb();

        $dbHost = $testDbHelper->getDbHost();
        if ($customPort) {
            $dbHost .= ':' . $customPort;
        }
        $page->findById('db_server')->setValue($dbHost);
        $page->findById('db_user')->setValue($testDbHelper->getDbUser());
        $page->findById('db_pass')->setValue('wrong');
        $page->findById('db_db')->setValue($testDbHelper->getDbName());
        $page->find('css', '.button_act')->click();
        sleep(1);
        $this->assertEquals('Can\'t connect to database.', $page->find('css', '.errorContainer .error')->getText());



        $page->findById('db_pass')->setValue($testDbHelper->getDbPass());
        $page->find('css', '.button_act')->click();

        sleep(1);

        $this->assertTrue($page->has('css', '#configSiteName'), 'Site name input is not available');

        $page->findById('configSiteName')->setValue('TestSiteName');
        $page->findById('configSiteEmail')->setValue('test@example.com');
        $page->findById('config_login')->setValue('admin');
        $page->findById('config_pass')->setValue('admin');
        $page->findById('config_email')->setValue('test@example.com');
        $page->findById('config_timezone')->selectOption('Europe/London');
        $page->find('css', '.button_act')->click();

        $this->assertNotContains('on line', $page->getContent());
        $this->assertFalse($page->has('css', '.error'));
        $this->assertEquals('ImpressPages CMS successfully installed.', $page->find('css', 'h1')->getText());

        $page->clickLink('Front page');

        sleep(1);

        $this->assertEquals(TEST_TMP_URL . 'installTest/', $session->getCurrentUrl());

        $this->assertNotContains('on line', $page->getContent());
        $this->assertFalse($page->has('css', '.error'));

        $headline = $page->find('css', '.homeHeadline');
        $this->assertNotEmpty($headline);
        $this->assertEquals('ImpressPages theme Blank', $headline->getText());

        $session->stop();

    }

    public function testCustomPort()
    {
        $this->testInstallCurrent(3306);
    }

    /**
     * @param \Behat\Mink\Element\DocumentElement $page
     */
    protected function assertNoErrors($page)
    {
        $this->assertNotContains('on line', $page->getContent());
        $this->assertFalse($page->has('css', '.error'));
    }

}