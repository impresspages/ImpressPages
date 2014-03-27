<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip\Internal\Install;


use PhpUnit\Helper\TestEnvironment;


class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        TestEnvironment::setup();
    }

    public function testCreateAndUseDatabase()
    {
        ipDb()->disconnect();

        // Create and use database if it doesn't exist:
        $config = include TEST_FIXTURE_DIR . 'config/default.php';
        unset($config['db']['database']);

        ipConfig()->set('db', $config['db']);

        $tempDbName = 'ip_test_create' . date('md_Hi_') . rand(1, 100);

        $database = ipDb()->fetchValue('SELECT DATABASE()');
        $this->assertEmpty($database);

        \Plugin\Install\Model::createAndUseDatabase($tempDbName);

        $database = ipDb()->fetchValue('SELECT DATABASE()');
        $this->assertEquals($tempDbName, $database);

        // Use database if it already exists:
        ipDb()->disconnect();

        ipConfig()->set('db', $config['db']);

        \Plugin\Install\Model::createAndUseDatabase($tempDbName);

        $database = ipDb()->fetchValue('SELECT DATABASE()');
        $this->assertEquals($tempDbName, $database);

        // Cleanup:
        ipDb()->execute('DROP DATABASE ' . $tempDbName);
        ipDb()->disconnect();
    }

    public function testImportData()
    {
        // Prepare environment:
        ipDb()->disconnect();

        $config = include TEST_FIXTURE_DIR . 'config/default.php';
        unset($config['db']['database']);
        ipConfig()->set('db', $config['db']);

        $tempDbName = 'ip_test_install' . date('md_Hi_') . rand(1, 100);
        \Plugin\Install\Model::createAndUseDatabase($tempDbName);

        // Create database structure:
        $config['db']['database'] = $tempDbName;

        \Plugin\Install\Model::createDatabaseStructure($config['db']['database'], $config['db']['tablePrefix']);

        $tables = ipDb()->fetchColumn('SHOW TABLES');
        $this->assertTrue(in_array('ip_page', $tables));
        $this->assertTrue(in_array('ip_plugin', $tables));

        // Import data:
        \Plugin\Install\Model::importData($config['db']['tablePrefix']);
        $languages = ipDb()->fetchAll('SELECT * FROM `ip_language`');
        $this->assertEquals(1, count($languages));
        $this->assertEquals('', $languages[0]['url']);

        // Cleanup:
        ipDb()->execute('DROP DATABASE ' . $tempDbName);
        ipDb()->disconnect();
    }

    public function testWriteConfig()
    {
        $emptyConfig = array();

        \Plugin\Install\Model::writeConfigFile($emptyConfig, TEST_TMP_DIR . 'config-testWriteConfig1.php');

        $config = include TEST_TMP_DIR . 'config-testWriteConfig1.php';

        $this->assertNotEmpty($config);
    }

    public function testConfigBeauty()
    {
        $sources = array(
'line1',
'line1
line2',
'line1
  line2
  line3'
    );



        $results = array(
'line1',
'line1
      line2',
'line1
        line2
        line3'
    );

        $addSpacesOnNewLines = new \ReflectionMethod('Plugin\Install\Model', 'addSpacesOnNewLines');
        $addSpacesOnNewLines->setAccessible(true);

        foreach ($sources as $key => $source) {
            $this->assertEquals($results[$key], $addSpacesOnNewLines->invoke(null, $source));
        }

    }

}
