<?php

namespace Tests\Functional;

use PhpUnit\Helper\TestEnvironment;

class AdminLoginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Behat\Mink\Session
     */
    protected $session;

    protected function setUp()
    {
        parent::setUp();

        TestEnvironment::setup();
        $this->session = \PhpUnit\Helper\Session::factory(__METHOD__);
    }

    public function tearDown()
    {
        if (getenv('TRAVIS')) {

            $sessionUrl = $this->session->getDriver()->getWebDriverSession()->getURL();
            $sauceSessionId = substr($sessionUrl, strrpos($sessionUrl, '/') + 1);

            $sauceReport = array(
                'passed' => $this->getStatus() == \PHPUnit_Runner_BaseTestRunner::STATUS_PASSED,
            );
            $json = json_encode($sauceReport);

            $template = 'curl -H "Content-Type:text/json" -s -X PUT -d \'%1$s\' http://%2$s:%3$s@saucelabs.com/rest/v1/%2$s/jobs/%4$s';
            $command = sprintf($template, $json, getenv('SAUCE_USERNAME'), getenv('SAUCE_ACCESS_KEY'), $sauceSessionId);
            echo "\n---\n";
            printf($template . "\n", $json, getenv('SAUCE_USERNAME'), 'SAUCE_ACCESS_KEY', $sauceSessionId);
            system($command);
            echo "\n---\n";
        }

        $this->session->stop();

        parent::tearDown();
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

        $adminHelper = new \PhpUnit\Helper\User\Admin($this->session, $installation);

        $adminHelper->login();

        $page = $this->session->getPage();
        $this->assertEmpty($page->find('css', '.ipsLoginButton'), 'Could not log in.');
        $this->assertNotEmpty($page->find('css', '.ipsContentPublish'));
    }

}
