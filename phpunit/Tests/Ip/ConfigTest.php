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
        parent::setup();
        TestEnvironment::initCode();
    }

    public function testBaseUrl()
    {
        $this->assertEquals('http://localhost/page', ipFileUrl('page'));
    }
}