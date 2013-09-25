<?php

namespace PhpUnit;

//class GeneralTestCase extends \PHPUnit_Extensions_Database_TestCase
class GeneralTestCase extends \PHPUnit_Extensions_Database_TestCase
{

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;



    static $init;

    /**
     * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new \PDO('mysql:host='.TEST_DB_HOST.';dbname='.TEST_DB_NAME, TEST_DB_USER, TEST_DB_PASS);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
                $dt = new \DateTime();
                $offset = $dt->format("P");
                self::$pdo->exec("SET time_zone='$offset';");
                self::$pdo->exec("SET CHARACTER SET utf8");

            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo);
        }

        return $this->conn;
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(TEST_FIXTURE_DIR.'empty.xml');
    }

    protected function setup()
    {
        if (!defined('IUL_TESTMODE')) {
            define('IUL_TESTMODE', 1);
        }
        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
        $fileSystemHelper->cpDir(TEST_FIXTURE_DIR.'InstallationDirs', TEST_TMP_DIR);
        $this->initInstallation();
        parent::setup();
    }
    
    protected function tearDown()
    {
        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
        parent::tearDown();
    }


    /**
     * @return \PhpUnit\Helper\Installation
     */
    public function initInstallation()
    {
        if (!static::$init) {
            static::$init = true;
            $this->initConstants();
            if (!defined('CMS')) {
                define('CMS', true); // make sure other files are accessed through this file.
            }
            if (!defined('FRONTEND')) {
                define('FRONTEND', true); // make sure other files are accessed through this file.
            }
            //because of PHPUnit magic, we have to repeat it on every test
            require_once(BASE_DIR.FRONTEND_DIR.'init.php');
        }
        global $parametersMod;
        $parametersMod = new Mock\ParametersMod();

    }

    private function initConstants()
    {


//constants for unit tests
        define('SESSION_NAME', 'testsession');  //prevents session conflict when two sites runs on the same server
// END GLOBAL


// DB
        define('DB_SERVER', TEST_DB_HOST); // eg, localhost
        define('DB_USERNAME', TEST_DB_USER);
        define('DB_PASSWORD', TEST_DB_PASS);
        define('DB_DATABASE', TEST_DB_NAME);
        define('DB_PREF', 'ip_');
// END DB

// GLOBAL
        define('BASE_DIR', TEST_BASE_DIR.TEST_CODEBASE_DIR); //root DIR with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
        define('BASE_URL', ''); //root url with trainling slash at the end. If you have moved your site to another place, change this line to correspond your new domain.
        define('IMAGE_DIR', 'phpunit/'.TEST_TMP_DIR.'image/');  //uploaded images directory
        define('TMP_IMAGE_DIR', 'phpunit/'.TEST_TMP_DIR.'image/tmp/'); //temporary images directory
        define('IMAGE_REPOSITORY_DIR', 'phpunit/'.TEST_TMP_DIR.'image/repository/'); //images repository. Used for TinyMCE and others where user can browse the images.
        define('FILE_DIR', 'phpunit/'.TEST_TMP_DIR.'file/'); //uploded files directory
        define('TMP_FILE_DIR', 'phpunit/'.TEST_TMP_DIR.'file/tmp/'); //temporary files directory
        define('FILE_REPOSITORY_DIR', 'phpunit/'.TEST_TMP_DIR.'file/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.
        define('VIDEO_DIR', 'phpunit/'.TEST_TMP_DIR.'video/'); //uploaded video directory
        define('TMP_VIDEO_DIR', 'phpunit/'.TEST_TMP_DIR.'video/tmp/'); //temporary video directory
        define('VIDEO_REPOSITORY_DIR', 'phpunit/'.TEST_TMP_DIR.'video/repository/'); //files repository. Used for TinyMCE and others where user can browse the files.
        define('AUDIO_DIR', 'phpunit/'.TEST_TMP_DIR.'audio/'); //uploaded audio directory
        define('TMP_AUDIO_DIR', 'phpunit/'.TEST_TMP_DIR.'audio/tmp/'); //temporary audio directory
        define('AUDIO_REPOSITORY_DIR', 'phpunit/'.TEST_TMP_DIR.'audio/repository/'); //audio repository. Used for TinyMCE and others where user can browse the files.

        define('DEVELOPMENT_ENVIRONMENT', 1); //displays error and debug information. Change to 0 before deployment to production server
        define('ERRORS_SHOW', 1);  //0 if you don't wish to display errors on the page
        define('ERRORS_SEND', ''); //insert email address or leave blank. If email is set, you will get an email when an error occurs.
// END GLOBAL

// BACKEND

        define('INCLUDE_DIR', 'ip_cms/includes/'); //system directory
        define('BACKEND_DIR', 'ip_cms/backend/'); //system directory
        define('FRONTEND_DIR', 'ip_cms/frontend/'); //system directory
        define('LIBRARY_DIR', 'ip_libs/'); //general classes and third party libraries
        define('MODULE_DIR', 'ip_cms/modules/'); //system modules directory
        define('CONFIG_DIR', 'ip_configs/'); //modules configuration directory
        define('PLUGIN_DIR', 'ip_plugins/'); //plugins directory
        define('THEME_DIR', 'ip_themes/'); //themes directory

        define('BACKEND_MAIN_FILE', 'admin.php'); //backend root file
        define('BACKEND_WORKER_FILE', 'ip_backend_worker.php'); //backend worker root file

// END BACKEND

// FRONTEND

        define('CHARSET', 'UTF-8'); //system characterset
        define('MYSQL_CHARSET', 'utf8');
        define('THEME', 'Blank'); //theme from themes directory
        define('DEFAULT_DOCTYPE', 'DOCTYPE_HTML5'); //look ip_cms/includes/Ip/View.php for available options.

        mb_internal_encoding(CHARSET);
        date_default_timezone_set('Africa/Bujumbura'); //PHP 5 requires timezone to be set.

// END FRONTEND

        define('SECURE_DIR', 'file/secure/'); //directory not accessible from the Internet
        define('TMP_SECURE_DIR', 'file/secure/tmp/'); //directory for temporary files. Not accessible from the Internet.
        define('MANUAL_DIR', 'file/manual/'); //Used for TinyMCE file browser and others tools where user manually controls all files.

    }


}