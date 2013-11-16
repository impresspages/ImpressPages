<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;

use \Ip\Db;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        parent::setup();
        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        \Ip\Config::init($config);

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Internal/Autoloader.php';
    }

    public function testConnect()
    {
        Db::disconnect();

        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        \Ip\Config::init($config);

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