<?php


namespace PhpUnit\Helper;


class MinkTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Behat\Mink\Session
     */
    private $session;

    protected function setUp()
    {
        TestEnvironment::setup();
    }

    /**
     * @return \Behat\Mink\Session
     */
    protected function session()
    {
        if (!$this->session) {
            $this->session = \PhpUnit\Helper\Session::factory(get_class($this) . '->' . $this->getName());
        }

        return $this->session;
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
//            echo "\n---\n";
//            printf($template . "\n", $json, getenv('SAUCE_USERNAME'), 'SAUCE_ACCESS_KEY', $sauceSessionId);
            exec($command);
//            echo "\n---\n";
        }

        $this->session->stop();
        $this->session = null;
    }
}

