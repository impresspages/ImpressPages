<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper;


class TestEnvironment {

    public static function initCode()
    {
        $config = include TEST_FIXTURE_DIR . 'ip_config/default.php';
        \Ip\Config::init($config);

        if (!defined('CMS')) {
            define('CMS', true); // make sure other files are accessed through this file.
        }
        if (!defined('FRONTEND')) {
            define('FRONTEND', true); // make sure other files are accessed through this file.
        }
        if (!defined('IUL_TESTMODE')) {
            define('IUL_TESTMODE', 1);
        }

        //because of PHPUnit magic, we have to repeat it on every test
        require_once BASE_DIR . FRONTEND_DIR . 'init.php';

        global $parametersMod;
        $parametersMod = new \PhpUnit\Mock\ParametersMod();
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