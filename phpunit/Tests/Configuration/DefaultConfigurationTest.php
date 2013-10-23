<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Configuration;


class DefaultConfigurationTest extends \PhpUnit\GeneralTestCase
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

    public function _testDefaultDirs()
    {
        $this->assertEquals('/var/www/localhost/file', \Ip\Config::fileDir());
    }

    public function _testAbsoluteFileDir()
    {
        $config = include __DIR__ . '/ipConfig-default.php';
        $config['fileDir'] = '/var/www/files';

        \Ip\Config::init($config);
        $this->assertEquals('/var/www/files', \Ip\Config::fileDir());
    }

    public function _testDefaultUrls()
    {
        $this->assertEquals('http://localhost/', \Ip\Config::homeUrl());
    }
}