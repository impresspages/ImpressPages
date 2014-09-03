<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;


use PhpUnit\Helper\TestEnvironment;

class RandomTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        TestEnvironment::setupCode();
    }

    public function testString()
    {
        $randomString = \Ip\Lib\Random::string(8);
        $this->assertEquals(8, strlen($randomString));
        $randomString2 = \Ip\Lib\Random::string(8);
        $this->assertNotEquals($randomString2, $randomString);

    }

}
