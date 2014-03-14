<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;


use PhpUnit\Helper\TestEnvironment;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setupCode();
    }

    public function testBaseUrl()
    {
        $this->assertEquals('http://localhost/page', ipFileUrl('page'));
    }
}