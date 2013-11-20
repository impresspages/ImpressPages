<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;


class ConfigTest extends \PhpUnit\GeneralTestCase
{
    public function setup()
    {
        parent::setup();
        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        //TODOX create an object
        ipGetConfig()->init($config);
    }

    public function testBaseUrl()
    {
        $this->assertEquals('http://localhost/page', ipGetConfig()->baseUrl('page'));
    }
}