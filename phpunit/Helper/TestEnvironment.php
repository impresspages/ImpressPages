<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper;


use Plugin\Install\TestService;

class TestEnvironment
{
    public static function setupOnce()
    {
        static $hasRun = false;

        if ($hasRun) {
            return false;
        }

        $hasRun = true;

        // create test database structure
        \Plugin\Install\TestService::setupTestDatabase(TEST_DB_NAME, 'ip_');
    }

    public static function setup()
    {
        static::filesSetup();
        static::setupCode();
    }

    public static function setupCode($configBasename = 'default.php')
    {
        require_once TEST_CODEBASE_DIR . 'Ip/Application.php';
        require_once TEST_CODEBASE_DIR . 'install/Constants.php';

        global $application;
        $application = new \Ip\Application(TEST_FIXTURE_DIR . 'config/' . $configBasename);

        if (!defined('IUL_TESTMODE')) {
            define('IUL_TESTMODE', 1);
        }

        //because of PHPUnit magic, we have to repeat it on every test
        $application->init();

        static::setupOnce();

        $_GET = array();
        $_POST = array();
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['SERVER_NAME'] = 'localhost';

        \Ip\ServiceLocator::addRequest(new \Ip\Request());
    }

    public static function filesSetup()
    {
        self::cleanupFiles();

        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->cpDir(TEST_FIXTURE_DIR.'InstallationDirs', TEST_TMP_DIR);

    }

    public static function cleanupFiles()
    {
        $fs = new \PhpUnit\Helper\FileSystem();
//        $fs->chmod(TEST_TMP_DIR, 0755);
        $fs->cleanDir(TEST_TMP_DIR);
        $fs->chmod(TEST_TMP_DIR . '.gitignore', 0664);
        $fs->chmod(TEST_TMP_DIR . 'readme.txt', 0664);
    }
}
