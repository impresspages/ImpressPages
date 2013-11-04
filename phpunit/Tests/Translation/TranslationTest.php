<?php
/**
 * @package   ImpressPages
 */
namespace Tests\Translation;


class TranslationTest extends \PHPUnit_Framework_TestCase
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
        \Ip\Translator::init();

        $this->assertEquals('non existent string', __('non existent string', 'ip'));
        $this->assertEquals('{{keyword}}', _k('keyword', 'ip'));
    }
}