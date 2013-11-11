<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Tests\Module\Install\Functional;

use \PhpUnit\Helper\TestEnvironment;

class InstallTest extends SeleniumInstallTest
{
    /**
     * @return \Behat\Mink\Session
     */
    protected function getSession()
    {
        $driver = new \Behat\Mink\Driver\GoutteDriver();

        $session = new \Behat\Mink\Session($driver);

        $session->start();

        return $session;
    }
}