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

        $session = \PhpUnit\Helper\Session::factory();

        $adminHelper = new \PhpUnit\Helper\User\Admin($session, $installation);

        $adminHelper->login();

        $page = $session->getPage();
        $this->assertEmpty($page->find('css', '.ipsLoginButton'), 'Could not log in.');
        $this->assertNotEmpty($page->find('css', '.ipsContentPublish'));

        if (getenv('TRAVIS')) {

            $sauceReport = array(
                'name' => __CLASS__ . '::' . __METHOD__,
                'passed' => true,
                // 'public' => 'public',
                // 'tags' => array('tag1', 'tag2'),
                'build' => getenv('TRAVIS_BUILD_NUMBER'),
                // 'custom-data' => array(
                //    'release' => '4.0',
                //),
            );

            $json = json_encode($sauceReport);

            $template = 'curl -H "Content-Type:text/json" -s -X PUT -d \'%1$s\' http://%2$s:%3$s@saucelabs.com/rest/v1/%2$s/jobs/%3$s';
            $command = sprintf($template, $json, getenv('SAUCE_USERNAME'), getenv('SAUCE_ACCESS_KEY'), getenv('TRAVIS_JOB_NUMBER'));
            system($command);
        }

    }

}
