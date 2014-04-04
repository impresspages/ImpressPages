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
        TestEnvironment::setupCode();
    }

    public function testTables()
    {
        $tables = ipDb()->fetchColumn('SHOW TABLES');
        $this->assertTrue(in_array('ip_widget', $tables), 'No required tables found in DB.');
    }

    public function testConnect()
    {
        ipDb()->disconnect();

        $config = include TEST_FIXTURE_DIR . 'config/default.php';
        $ipConfig = new \Ip\Config($config);
        \Ip\ServiceLocator::setConfig($ipConfig);

        $this->assertNotEmpty(ipConfig()->get('db'));

        ipDb()->getConnection();

        $this->assertEmpty(ipConfig()->get('db'));
    }

    public function testDisconnect()
    {
        ipDb()->disconnect();

        try {
            ipDb()->getConnection();
            $this->assertFalse(true, 'Not disconnected');
        } catch (\Ip\Exception\Db $e) {
            $this->assertTrue(true);
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
        $value = ipDb()->fetchValue('SELECT `abbreviation` FROM ' . ipTable('language') . ' WHERE `code` = ?', array('en'));
        $this->assertEquals('EN', $value);

        $value = ipDb()->fetchValue('SELECT `abbreviation` FROM ' . ipTable('language') . ' WHERE `code` = ?', array('not/existent/code/'));
        $this->assertNull($value);
    }

    public function testSelectColumn()
    {
        $values = ipDb()->selectColumn('permission', 'permission', array('administratorId' => 1));
        $this->assertEquals(json_encode($values), json_encode(array('Super admin')));

        $values = ipDb()->selectColumn('permission', 'permission', array('administratorId' => 77));
        $this->assertEquals(array(), $values);
    }

    public function testInstallDbStructure()
    {
        $installStructureFile = ipFile('install/Plugin/Install/sql/structure.sql');

        $sql = file_get_contents($installStructureFile);

        $this->assertNotEmpty($sql);

        $this->assertEquals(false, strpos($sql, 'CREATE TABLE IF NOT EXISTS'));
    }

}
