<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;

use \Ip\Db;
use PhpUnit\Helper\TestEnvironment;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        parent::setup();

        TestEnvironment::initCode();
    }

    public function testConnect()
    {
        Db::disconnect();

        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        //TODOX create object
        ipGetConfig()->init($config);

        $this->assertNotEmpty(ipGetConfig()->getRaw('db'));

        Db::getConnection();

        $this->assertEmpty(ipGetConfig()->getRaw('db'));
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

    public function testException()
    {
        try {
            $file = __FILE__; $line = __LINE__ + 1;
            Db::fetchAll('SELECT * FROM `nonExistingTable`');
            $this->assertTrue(false, 'Exception was not thrown.');
        } catch (\PDOException $e) {
            $this->assertEquals($file, $e->getFile());
            $this->assertEquals($line, $e->getLine());
        }

        try {
            $file = __FILE__; $line = __LINE__ + 1;
            Db::update('nonExistingTable', array('id' => 0), array('dummy' => 'dummy'));
            $this->assertTrue(false, 'Exception was not thrown.');
        } catch (\PDOException $e) {
            $this->assertEquals($file, $e->getFile());
            $this->assertEquals($line, $e->getLine());
        }

    }

    public function testFetchValue()
    {
        $value = Db::fetchValue('SELECT `d_short` FROM `' . DB_PREF . 'language` WHERE `code` = ?', array('en'));
        $this->assertEquals('EN', $value);
    }

}
