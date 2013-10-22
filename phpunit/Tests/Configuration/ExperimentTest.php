<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Configuration;


class ExperimentTest extends \PhpUnit\GeneralTestCase
{
    public function setup()
    {
        parent::setup();
        $config = include __DIR__ . '/ipConfig-default.php';
        \Ip\Config::init($config);
    }

    public function testLoadConfiguration()
    {
        $this->assertEquals('localhost', \Ip\Config::getRaw('host'));
    }
}