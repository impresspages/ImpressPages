<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper;


class TestEnvironment {

    public static function initCode($configBasename = 'default.php')
    {
        require_once TEST_CODEBASE_DIR . 'Ip/Application.php';

        $app = new \Ip\Application(TEST_FIXTURE_DIR . 'ip_config/' . $configBasename);

        if (!defined('IUL_TESTMODE')) {
            define('IUL_TESTMODE', 1);
        }

        //because of PHPUnit magic, we have to repeat it on every test
        $app->init();

        $_GET = array();
        $_POST = array();
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['SERVER_NAME'] = 'localhost';
    }

    public static function prepareFiles()
    {
        self::cleanupFiles();

        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->cpDir(TEST_FIXTURE_DIR.'InstallationDirs', TEST_TMP_DIR);

    }

    public static function cleanupFiles()
    {
        $fs = new \PhpUnit\Helper\FileSystem();
        $fs->chmod(TEST_TMP_DIR, 0755);
        $fs->cleanDir(TEST_TMP_DIR);
        $fs->chmod(TEST_TMP_DIR . '.gitignore', 0664);
        $fs->chmod(TEST_TMP_DIR . 'readme.txt', 0664);
    }
}