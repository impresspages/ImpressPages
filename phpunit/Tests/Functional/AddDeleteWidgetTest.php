<?php

namespace Tests\Functional;

use PhpUnit\Helper\TestEnvironment;

class AddDeleteWidgetTest extends \PHPUnit_Framework_TestCase
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

        return;


        $ipActions = new \PhpUnit\Helper\IpActions($this, $installation);
        $ipActions->login();

        $this->windowMaximize();

        $ipActions->addWidget('IpTitle');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpText');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpSeparator');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpTextImage');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpImage');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpImageGallery');
        $ipActions->selectFirstFileInRepository();
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpLogoGallery');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpFile');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpTable');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpHtml');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpFaq');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();
        $ipActions->addWidget('IpForm');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $ipActions->addWidget('IpColumns');
        $this->assertNoErrors();
        $ipActions->cancelWidget();
        $this->assertNoErrors();

    }


}
