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

        $this->assertEquals('non existent string', __('non existent string', 'ip'));
        $this->assertEquals('{{keyword}}', __k('keyword', 'ip'));
    }
}

function __($text, $domain)
{
    return $text;
}

function _x($text, $context, $domain)
{
    return $text;
}

function _n($single, $plural, $number, $domain)
{

}

function _nx($single, $plural, $number, $context, $domain)
{

}

function _k($text, $domain)
{
    return '{{' . $text .'}}';
}