<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;


class DefaultConfigurationTest extends \PhpUnit\GeneralTestCase
{
    public function setup()
    {
        parent::setup();
        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        \Ip\Config::init($config);
    }

    public function testBaseUrl()
    {
        $this->assertEquals('http://localhost/page', \Ip\Config::baseUrl('page'));
    }
}