<?php
/**
 * @package   ImpressPages
 */

class DefaultEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     *   Scenario: Loading default test environment
     *    Given I am in a test
     *    When I load test environment
     *    Then Default test constants should be set
     */
    public function testDefaultTestEnvironment()
    {
        \PhpUnit\Helper\TestEnvironment::initCode();
        $this->assertEquals(realpath(TEST_CODEBASE_DIR) . '/', \Ip\Config::getRaw('BASE_DIR'));
    }
}