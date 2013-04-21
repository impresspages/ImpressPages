<?php

namespace PhpUnit;

//class GeneralTestCase extends \PHPUnit_Extensions_Database_TestCase
class CoreTestCase extends \PHPUnit_Framework_TestCase
{
    protected $installation;

    protected function setup()
    {
        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
        $this->initInstallation();
    }
    
    protected function tearDown()
    {
        $fileSystemHelper = new \PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
    }

    /**
     * @return \PhpUnit\Helper\Installation
     */
    public function initInstallation()
    {
        if (!$this->installation) {
            $installation = new \PhpUnit\Helper\Installation(); //development version
            $installation->install();


            //core bootstrap

            if (!defined('CMS')) {
                define('CMS', true);
            }
            if (!defined('FRONTEND')) {
                define('FRONTEND', true);
            }

            require ($installation->getInstallationDir().'/ip_config.php');

            require_once($installation->getInstallationDir().FRONTEND_DIR.'init.php');
            //end core bootstrap

            $this->installation = $installation;
            global $site;
            if (!defined('BACKEND')) {
                define ('BACKEND', true);
            }
            $_SERVER['REQUEST_URI'] = $installation->getInstallationUrl();

            $site->init(); //language parameters can't be accessed without this
        }
        return $this->installation;
    }

}