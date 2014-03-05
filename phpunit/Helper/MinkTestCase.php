<?php


namespace PhpUnit\Helper;


class MinkTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Behat\Mink\Session
     */
    private $session;

    protected $timeout = 5;

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
            $this->session = static::startSession(get_class($this) . '::' . $this->getName());
        }

        return $this->session;
    }

    /**
     * @return \Behat\Mink\Element\DocumentElement
     */
    protected function page()
    {
        return $this->session()->getPage();
    }

    public function tearDown()
    {
        static::stopSession($this->session, $this->getStatus() == \PHPUnit_Runner_BaseTestRunner::STATUS_PASSED);

        $this->session = null;

        TestEnvironment::cleanupFiles();
    }

    /**
     * @param string $cssSelector
     * @return \Behat\Mink\Element\NodeElement|null
     */
    protected function find($cssSelector)
    {
        $element = $this->session()->waitForElementPresent('css', $cssSelector);
        $this->assertNotEmpty($element, "Element $cssSelector not found");
        return $element;
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

        // $session->wait();


        return $session;
    }

    /**
     * @param \Behat\Mink\Session $session
     * @param bool $hasPassed
     */
    public static function stopSession($session, $hasPassed)
    {
        if (!$session) {
            return;
        }

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

    /**
     * @param $lambda
     * @param int|null $timeout
     * @return mixed
     */
    public function spin($lambda, $timeout = null)
    {
        if (!$timeout) {
            $timeout = $this->timeout;
        }

        $start = microtime(true);

        do {
            try {
                $result = $lambda($this);
            } catch (Exception $e) {
                // do nothing
            }

        } while ($result === null && microtime(true) - $start < $timeout);

        return $result;
    }

    /**
     * @param string $cssSelector
     * @param int|null $timeout
     * @return \Behat\Mink\Element\NodeElement|null
     */
    public function waitForElementPresent($cssSelector, $timeout = null)
    {
        $context = $this;
        $result = $this->spin(
            function () use ($context, $cssSelector) {
                return $context->getPage()->find('css', $cssSelector);
            },
            $timeout
        );

        $this->assertNotEmpty($result, "Element $cssSelector not found");

        return $result;
    }

    /**
     * @param string $cssSelector
     * @param int|null $timeout
     * @return bool
     */
    public function waitForElementNotPresent($cssSelector, $timeout = null)
    {
        $context = $this;
        $result = $this->spin(
            function () use ($context, $cssSelector) {
                $element = $context->getPage()->find('css', $cssSelector);
                if (!$element) {
                    return true;
                }
            }
        );

        $this->assertTrue($result, "Element $cssSelector is present");

        return $result ? true : false;
    }

}

