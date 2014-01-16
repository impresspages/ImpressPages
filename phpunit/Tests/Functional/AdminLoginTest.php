<?php

namespace Tests\Functional;

use PhpUnit\Helper\TestEnvironment;

class AdminLoginTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setup();
    }

    /**
     * @group Sauce
     * @group Selenium
     */
    public function testLogin()
    {
        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();

        // init Mink:
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

        $session = new \Behat\Mink\Session($driver);
        $session->start();

        $installationUrl = $installation->getInstallationUrl();

        $session->visit($installationUrl . 'admin');

        $loginPage = $session->getPage();

        $loginButton = $loginPage->find('css', '.ipsLoginButton');

        $this->assertNotEmpty($loginButton);

        $loginField = $loginPage->find('css', '.form-control[name=login]');
        $loginField->setValue($installation->getAdminLogin());

        $passwordField = $loginPage->find('css', '.form-control[name=password]');
        $passwordField->setValue($installation->getAdminPass());

        $loginButton->click();

        $session->wait(10000,"typeof $ !== 'undefined' && $('.ipActionPublish').size() > 0");
        $page = $session->getPage();
        $this->assertEmpty($page->find('css', '.ipsLoginButton'), 'Could not log in.');
        $this->assertNotEmpty($page->find('css', '.ipActionPublish'));


    }


}
