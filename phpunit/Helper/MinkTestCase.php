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

        echo "\n\n\n" . get_class($this) . '::' . $this->getName() . "\n\n";
    }

    /**
     * @return \Behat\Mink\Session
     */
    protected function session()
    {
        if (!$this->session) {
            $this->session = static::startSession(get_class($this) . '::' . $this->getName());
        }

        return $this->session;
    }

    public function tearDown()
    {
        static::stopSession($this->session, $this->getStatus() == \PHPUnit_Runner_BaseTestRunner::STATUS_PASSED);

        $this->session = null;
    }


    /**
     * @return \Behat\Mink\Session
     */
    public static function startSession($testName)
    {
        // init Mink:
        if (getenv('TRAVIS')) {
            // $url = sprintf('http://%s:%s@localhost:4445/wd/hub', getenv('SAUCE_USERNAME'), getenv('SAUCE_ACCESS_KEY'));
            $url = sprintf(
                'http://%s:%s@ondemand.saucelabs.com/wd/hub',
                getenv('SAUCE_USERNAME'),
                getenv('SAUCE_ACCESS_KEY')
            );
            $desiredCapabilities = array(
                'name' => $testName,
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


        return $session;
    }

    /**
     * @param \Behat\Mink\Session $session
     * @param bool $hasPassed
     */
    public static function stopSession($session, $hasPassed)
    {
        if (getenv('TRAVIS')) {
            $sessionUrl = $session->getDriver()->getWebDriverSession()->getURL();
            $sauceSessionId = substr($sessionUrl, strrpos($sessionUrl, '/') + 1);

            $sauceReport = array(
                'passed' => $hasPassed,
            );
            $json = json_encode($sauceReport);

            $template = 'curl -H "Content-Type:text/json" -s -X PUT -d \'%1$s\' http://%2$s:%3$s@saucelabs.com/rest/v1/%2$s/jobs/%4$s';
            $command = sprintf($template, $json, getenv('SAUCE_USERNAME'), getenv('SAUCE_ACCESS_KEY'), $sauceSessionId);
//            echo "\n---\n";
//            printf($template . "\n", $json, getenv('SAUCE_USERNAME'), 'SAUCE_ACCESS_KEY', $sauceSessionId);
            exec($command);
//            echo "\n---\n";
        }

        try {
            $session->stop();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

}

