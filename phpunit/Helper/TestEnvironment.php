<?php
/**
 * @package   ImpressPages
 */

namespace PhpUnit\Helper;


class TestEnvironment {

    public static function initCode($configBasename = 'default.php')
    {
        $config = include TEST_FIXTURE_DIR . 'ip_config/' . $configBasename;
        \Ip\Config::init($config);

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/autoloader.php';

        if (!defined('IUL_TESTMODE')) {
            define('IUL_TESTMODE', 1);
        }

        require_once \Ip\Config::getCore('CORE_DIR') . 'Ip/Core/Application.php';
        //because of PHPUnit magic, we have to repeat it on every test
        \Ip\Core\Application::init();

        global $parametersMod;
        $parametersMod = new \PhpUnit\Mock\ParametersMod();

        $_GET = array();
        $_POST = array();
        $_SERVER = array(
            'REQUEST_URI' => '/',
            'REQUEST_METHOD' => 'GET',
            'SERVER_PORT' => 80,
            'SERVER_NAME' => 'localhost',
        );
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