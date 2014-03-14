<?php
/**
 * @package   ImpressPages
 */
namespace Tests\Translator;


class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     *   Scenario: Loading default test environment
     *    Given I am in a test
     *    When I load test environment
     *    Then Default test constants should be set
     */
    public function testDefaultTestEnvironment()
    {
        \PhpUnit\Helper\TestEnvironment::setupCode();

        $this->assertEquals('non existent string', __('non existent string', 'ip'));
    }
}