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
        $this->setAdminLogin('admin');
        $this->setAdminPass('admin');

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
        
        $this->createDatabase($this->getDbName());

        $netHelper = new \IpUpdate\Library\Helper\Net();
        $archive = TEST_TMP_DIR.'ImpressPages_'.$this->getVersion().'.zip';
        $migrationModel = new \IpUpdate\Library\Model\Migration();
        $script = $migrationModel->getScriptToVersion($this->getVersion());
        $netHelper->downloadFile($script->getDownloadUrl(), $archive);

        mkdir($this->getInstallationDir());
        
        if (!class_exists('PclZip')) {
            require_once(TEST_BASE_DIR.'Helper/PclZip.php');
        }
        $zip = new \PclZip($archive);
        $success = $zip->extract(PCLZIP_OPT_PATH, $this->getInstallationDir(), PCLZIP_OPT_REMOVE_PATH, $this->getSubdir($this->getVersion()));

        if ($success == 0) {
            throw new \Exception("Unrecoverable error: ".$zip->errorInfo(true));
        }
        
        file_put_contents($this->getInstallationDir().'.htaccess', 'allow from all');
        
        // INIT CURL
        $ch = curl_init();
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $answer = curl_exec($ch);

        

        // RUN CRON
        curl_setopt($ch, CURLOPT_URL, $this->getInstallationUrl().'/ip_cron.php');
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $answer = curl_exec($ch);
        
        $this->installed = true;
        
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
    
    public function setupUpdate($destinationVersion) 
    {
        if (!$this->isInstalled()) {
            throw new \Exception("system is not installed");
        }
        
        $netHelper = new \IpUpdate\Library\Helper\Net();
        $archive = TEST_TMP_DIR.'ImpressPages_'.$destinationVersion.'.zip';
        $migrationModel = new \IpUpdate\Library\Model\Migration();
        $script = $migrationModel->getScriptToVersion($destinationVersion);
        $netHelper->downloadFile($script->getDownloadUrl(), $archive);

        $fs = new \IpUpdate\Library\Helper\FileSystem();
        $fs->rm($this->getInstallationDir().'update');
        mkdir($this->getInstallationDir().'update');
        
        
        if (!class_exists('PclZip')) {
            require_once(TEST_BASE_DIR.'Helper/PclZip.php');
        }
        $zip = new \PclZip($archive);
        $success = $zip->extract(PCLZIP_OPT_PATH, $this->getInstallationDir().'update', PCLZIP_OPT_REMOVE_PATH, $this->getSubdir($destinationVersion).'/update');

        if ($success == 0) {
            throw new \Exception("Unrecoverable error: ".$zip->errorInfo(true));
        }
        
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
    
    public function setAdminLogin($adminLogin)
    {
        $this->adminLogin = $adminLogin;
    }
    
    public function setAdminPass($adminPass)
    {
        $this->adminPass = $adminPass;
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