<?php

namespace IpUpdate\PhpUnit\Helper;

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


class Installation
{
    private $version;
    private $installationDir;
    private $installationUrl;
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbName;
    private $dbPrefix;
    private $adminLogin;
    private $adminPass;
    private $siteName;
    private $siteEmail;
    private $siteTimeZone;

    private $installed;
    /**
     * @param string$version
     */
    public function __construct($version)
    {
        $versionDir = str_replace('.', '_', $version);
        $this->version = $version; //version dosen't have a setter, because it can't be changed after object has been created.

        $this->setInstallationDir(TEST_TMP_DIR.$versionDir.'/');
        $this->setInstallationUrl(TEST_TMP_URL.$versionDir.'/');
        $this->setDbHost('localhost');
        $this->setDbUser(TEST_DB_USER);
        $this->setDbPass(TEST_DB_PASS);
        $this->setDbName('test_'.time());
        $this->setDbPrefix('ipt_');
        $this->setSiteName('TestSite');
        $this->setSiteEmail('test@example.com');
        $this->setSiteTimeZone('Europe/London');

        $this->installed = false;
    }


    /**
     *
     * @param string $version
     */
    public function install()
    {
        if ($this->isInstalled()) {
            throw new \Exception("Already intalled");
        }
        require_once(TEST_BASE_DIR.'Helper/PclZip.php');

        $this->createDatabase($this->getDbName());

        $archive = $this->getArchiveFileName($this->getVersion());

        mkdir($this->getInstallationDir());
        
        $zip = new \PclZip($archive);
        $success = $zip->extract(PCLZIP_OPT_PATH, $this->getInstallationDir(), PCLZIP_OPT_REMOVE_PATH, $this->getSubdir($this->getVersion()));

        if ($success == 0) {
            throw new \Exception("Unrecoverable error: ".$zip->errorInfo(true));
        }
        
        // INIT CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "");

        // INSTALL DATABASE
        $data = array (
                'action' => 'create_database',
                'server' => $this->getDbHost(),
                'db_user' => $this->getDbUser(),
                'db_pass' => $this->getDbPass(),
                'db' => $this->getDbName(),
                'prefix' => $this->getDbPrefix()
        );
        $fieldsString = '';
        foreach($data as $key=>$value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString,'&');
        curl_setopt($ch, CURLOPT_URL, $this->getInstallationUrl().'install/worker.php');
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        $answer = curl_exec($ch);

        // SETUP CONFIG FILE

        $data = array (
                'action' => 'config',
                'install_login' => $this->getAdminLogin(),
                'install_pass' => $this->getAdminPass(),
                'email' => '',
                'timezone' => $this->getSiteTimezone(),
                'site_name' => $this->getSiteName(),
                'site_email' => $this->getSiteEmail()
        );

        $fieldsString = '';
        foreach($data as $key=>$value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString,'&');
        curl_setopt($ch, CURLOPT_URL, $this->getInstallationUrl().'install/worker.php');
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        $answer = curl_exec($ch);

        // RUN CRON
        curl_setopt($ch, CURLOPT_URL, $this->getInstallationUrl().'/ip_cron.php');
        curl_setopt($ch, CURLOPT_POST, count($data));
        $answer = curl_exec($ch);
    }

    public function uninstall()
    {
        if (!$this->isInstalled()) {
            throw new \Exception("system is not installed");
        }

        $this->dropDatabase($this->getDbName());

        $fs = new \IpUpdate\Library\Helper\FileSystem();
        $fs->rm($this->getInstallationDir());
    }


    public function getArchiveFileName($version)
    {
        $fileName = TEST_FIXTURE_DIR.'Package/'.$this->getSubdir($version).'.zip';

        if (file_exists($fileName)) {
            return $fileName;
        } else {
            throw new \Exception("Version ".$version." package does not exist");
        }
    }
    
    public function getSubdir($version)
    {
        return 'ImpressPages_'.str_replace('.', '_', $version);
    }

    public function isInstalled()
    {
        return $this->installed;
    }

    public function setInstallationDir($dir)
    {
        $this->installationDir = $dir;
    }

    public function setInstallationUrl($url)
    {
        $this->installationUrl = $url;
    }

    public function setDbHost($dbHost)
    {
        $this->dbHost = $dbHost;
    }

    public function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;
    }

    public function setDbPass($dbPass)
    {
        $this->dbPass = $dbPass;
    }

    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

    public function setDbPrefix($dbPrefix)
    {
        $this->dbPrefix = $dbPrefix;
    }

    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
    }

    public function setSiteEmail($siteEmail)
    {
        $this->siteEmail = $siteEmail;
    }

    public function setSiteTimeZone($timeZone)
    {
        $this->siteTimeZone = $timeZone;
    }

    //
    // Getters
    //

    public function getVersion()
    {
        return $this->version;
    }

    public function getInstallationDir()
    {
        return $this->installationDir;
    }

    public function getInstallationUrl()
    {
        return $this->installationUrl;
    }

    public function getDbHost()
    {
        return $this->dbHost;
    }

    public function getDbUser()
    {
        return $this->dbUser;
    }

    public function getDbPass()
    {
        return $this->dbPass;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

    public function getDbPrefix()
    {
        return $this->dbPrefix;
    }

    public function getAdminLogin()
    {
        return $this->adminLogin;
    }

    public function getAdminPass()
    {
        return $this->adminPass;
    }

    public function getSiteName()
    {
        return $this->siteName;
    }

    public function getSiteEmail()
    {
        return $this->siteEmail;
    }

    public function getSiteTimeZone()
    {
        return $this->siteTimeZone;
    }

    //
    // private methods
    //

    private function createDatabase($dbName)
    {
        $connection = mysql_connect(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS);
        if(!$connection) {
            throw new \Exception('Can\'t connect to database.');
        }

        mysql_query("CREATE DATABASE `".$dbName."` CHARACTER SET utf8", $connection);
        mysql_query("ALTER DATABASE `".$dbName."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;", $connection);

        mysql_close($connection);
    }


    private function dropDatabase($dbName)
    {
        $connection = mysql_connect(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS);
        if(!$connection) {
            throw new \Exception('Can\'t connect to database.');
        }

        mysql_query("DROP DATABASE `".$dbName."`", $connection);

        mysql_close($connection);

    }




}