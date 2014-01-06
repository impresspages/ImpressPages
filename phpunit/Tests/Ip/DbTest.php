<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip;

use PhpUnit\Helper\TestEnvironment;

class DbTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        parent::setup();

        TestEnvironment::initCode();
    }

    public function testTables()
    {
        $tables = ipDb()->fetchColumn('SHOW TABLES');
        //* TODOX remove
        var_export($tables);
        echo __FILE__ . ':' . (__LINE__ - 2);
        exit();
        //*/
    }

    public function testConnect()
    {
        ipDb()->disconnect();

        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        $ipConfig = new \Ip\Config($config);
        \Ip\ServiceLocator::setConfig($ipConfig);

        $this->assertNotEmpty(ipConfig()->getRaw('db'));

        ipDb()->getConnection();

        $this->assertEmpty(ipConfig()->getRaw('db'));
    }

    public function testDisconnect()
    {
        ipDb()->disconnect();

        try {
            ipDb()->getConnection();
            $this->assertFalse(true, 'Not disconnected');
        } catch (\Ip\CoreException $e) {
            $this->assertEquals($e->getCode(), \Ip\CoreException::DB);
        }
    }

    public function testException()
    {
        try {
            $file = __FILE__; $line = __LINE__ + 1;
            ipDb()->fetchAll('SELECT * FROM `nonExistingTable`');
            $this->assertTrue(false, 'Exception was not thrown.');
        } catch (\PDOException $e) {
            $this->assertEquals($file, $e->getFile());
            $this->assertEquals($line, $e->getLine());
        }

        try {
            $file = __FILE__; $line = __LINE__ + 1;
            ipDb()->update('nonExistingTable', array('id' => 0), array('dummy' => 'dummy'));
            $this->assertTrue(false, 'Exception was not thrown.');
        } catch (\PDOException $e) {
            $this->assertEquals($file, $e->getFile());
            $this->assertEquals($line, $e->getLine());
        }

    }

    public function testFetchValue()
    {
        $value = ipDb()->fetchValue('SELECT `d_short` FROM ' . ipTable('language') . ' WHERE `code` = ?', array('en'));
        $this->assertEquals('EN', $value);
    }

}
