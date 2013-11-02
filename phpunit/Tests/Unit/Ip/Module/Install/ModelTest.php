<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Ip\Module\Install;

use \Ip\Module\Install\Model;
use PhpUnit\Helper\TestEnvironment;
use \Ip\Db;


class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateAndUseDatabase()
    {
        TestEnvironment::initCode();
        Db::disconnect();

        // Create and use database if it doesn't exist:
        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        unset($config['db']['database']);
        \Ip\Config::_setRaw('db', $config['db']);

        $tempDbName = 'ip_test_create' . date('md_hi_') . rand(1, 100);

        $database = Db::fetchValue('SELECT DATABASE()');
        $this->assertEmpty($database);

        Model::createAndUseDatabase($tempDbName);

        $database = Db::fetchValue('SELECT DATABASE()');
        $this->assertEquals($tempDbName, $database);

        // Use database if it already exists:
        Db::disconnect();

        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        unset($config['db']['database']);
        \Ip\Config::_setRaw('db', $config['db']);

        Model::createAndUseDatabase($tempDbName);

        $database = Db::fetchValue('SELECT DATABASE()');
        $this->assertEquals($tempDbName, $database);

        // Cleanup:
        Db::execute('DROP DATABASE ' . $tempDbName);

        Db::disconnect();
    }

    public function testInstallDatabase()
    {
        TestEnvironment::initCode();
        Db::disconnect();

        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        unset($config['db']['database']);
        \Ip\Config::_setRaw('db', $config['db']);

        $tempDbName = 'ip_test_install' . date('md_hi_') . rand(1, 100);

        Model::createAndUseDatabase($tempDbName);

        $config['db']['database'] = $tempDbName;

        Model::installDatabase($config['db']);

        $tables = Db::fetchColumn('SHOW TABLES');

        $this->assertArrayHasKey('labas', $tables);
    }
} 