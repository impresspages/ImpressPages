<?php

namespace PhpUnit\Helper;

/**
 * @package ImpressPages
 *
 *
 */


class Session
{

    /**
     * @return \Behat\Mink\Session
     */
    public static function factory($testName)
    {
        echo "\n\n\nSTART {$testName}\n\n";
        
        // init Mink:
        if (getenv('TRAVIS')) {
            // $url = sprintf('http://%s:%s@localhost:4445/wd/hub', getenv('SAUCE_USERNAME'), getenv('SAUCE_ACCESS_KEY'));
            $url = sprintf('http://%s:%s@ondemand.saucelabs.com/wd/hub', getenv('SAUCE_USERNAME'), getenv('SAUCE_ACCESS_KEY'));
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


}
