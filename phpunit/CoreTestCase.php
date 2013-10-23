<?php

namespace PhpUnit;

/**
 * class GeneralTestCase extends \PHPUnit_Extensions_Database_TestCase
 * Class CoreTestCase
 * @deprecated
 * @package PhpUnit
 */
class CoreTestCase extends \PHPUnit_Framework_TestCase
{
    static $init;
    static $connection;

    protected function setup()
    {
        \PhpUnit\Helper\TestEnvironment::prepareFiles();
        \PhpUnit\Helper\TestEnvironment::initCode();
    }
    
    protected function tearDown()
    {
        \PhpUnit\Helper\TestEnvironment::cleanupFiles();
    }

}