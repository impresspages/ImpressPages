<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Unit\Ip;

use \Ip\Db;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        parent::setup();
        $config = include __DIR__ . '/ipConfig-default.php';
        \Ip\Config::init($config);

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/autoloader.php';
    }

    public function testConnect()
    {
        $this->assertNotEmpty(\Ip\Config::getRaw('db'));

        Db::getConnection();

        $this->assertEmpty(\Ip\Config::getRaw('db'));
    }

    public function testDisconnect()
    {
        Db::disconnect();

        try {
            Db::getConnection();
            $this->assertFalse(true, 'Not disconnected');
        } catch (\Ip\CoreException $e) {
            $this->assertEquals($e->getCode(), \Ip\CoreException::DB);
        }
    }

} 