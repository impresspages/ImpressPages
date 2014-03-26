<?php
/**
 * @package   ImpressPages
 */

class DefaultEnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultTestEnvironment()
    {
        \PhpUnit\Helper\TestEnvironment::setup();

        $this->assertEquals(realpath(TEST_CODEBASE_DIR), ipConfig()->get('baseDir'));

        $this->assertTrue(file_exists(ipFile('file/repository/')), 'file/repository doesn\'t exist.');

        $this->assertNotEmpty(\Ip\ServiceLocator::dispatcher(), 'Dispatcher not loaded.');
    }

    public function testDatabaseStructure()
    {
        \PhpUnit\Helper\TestEnvironment::setup();

        $tables = ipDb()->fetchColumn('SHOW TABLES');

        $this->assertTrue(in_array('ip_page', $tables));
        $this->assertTrue(in_array('ip_pageStorage', $tables));
    }


}
