<?php

namespace PhpUnit\Helper;

/**
 * @package ImpressPages
 *
 *
 */

use \Plugin\Install\Model as InstallModel;

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
    private $cf;
    private $testDbHelper;
    private $defaultConfig;

    /**
     * @var true if this installation represents current development version
     */
    private $developmentVersion;

    private $installed;

    /**
     * @param null $version
     */
    public function __construct($version = null)
    {
        if ($version === null) {
            $this->developmentVersion = true;
            $version = \PhpUnit\Helper\Service::getLatestVersion();
        } else {
            $this->developmentVersion = false;
        }

        $versionDir = str_replace('.', '_', $version);
        $this->version = $version; //version doesn't have a setter, because it can't be changed after object has been created.

        $this->setInstallationDir(TEST_TMP_DIR.$versionDir.'/');
        $this->setInstallationUrl(TEST_TMP_URL.$versionDir.'/');
        $this->setDbHost('localhost');
        $this->setDbUser(TEST_DB_USER);
        $this->setDbPass(TEST_DB_PASS);
        $this->setDbPrefix('ip_');
        $this->setSiteName('TestSite');
        $this->setSiteEmail('test@example.com');
        $this->setSiteTimeZone('Europe/London');
        $this->setAdminLogin('admin');
        $this->setAdminPass('admin');

        $this->defaultConfig = array();

        $this->installed = false;
    }

    public function setDefaultConfig($defaultConfig)
    {
        $this->defaultConfig = $defaultConfig;
    }



    /**
     * @throws \Exception
     */
    public function install()
    {
        if ($this->isInstalled()) {
            throw new \Exception("Already installed");
        }

        $testDbHelper = new \PhpUnit\Helper\TestDb();
        $this->setDbName($testDbHelper->getDbName());
        $this->setDbHost($testDbHelper->getDbHost());
        $this->setDbUser($testDbHelper->getDbUser());
        $this->setDbPass($testDbHelper->getDbPass());
        $this->testDbHelper = $testDbHelper; //database will be destroyed on this object destruction;

        $this->putInstallationFiles($this->getInstallationDir());

        InstallModel::setInstallationDir($this->getInstallationDir());
        InstallModel::createAndUseDatabase($this->getDbName());
        InstallModel::createDatabaseStructure($this->getDbName(), $this->getDbPrefix());
        InstallModel::importData($this->getDbPrefix());
        if ($this->getVersion() == '4.0.0') {
            \Ip\Internal\Administrators\Service::add($this->getAdminLogin(), $this->getSiteEmail(), $this->getAdminPass());
        } else {
            InstallModel::insertAdmin($this->getAdminLogin(), $this->getSiteEmail(), $this->getAdminPass());
        }
        InstallModel::setSiteEmail($this->getSiteEmail());
        InstallModel::setSiteName($this->getSiteName());

        $config = array();
        $config['sessionName'] = 'ses' . rand();
        $config['timezone'] = $this->getSiteTimeZone();
        $config['db'] = array(
            'hostname' => $this->getDbHost(),
            'username' => $this->getDbPass(),
            'password' => $this->getDbPass(),
            'database' => $this->getDbName(),
            'tablePrefix' => $this->getDbPrefix(),
            'charset' => 'utf8'
        );
        $config = array_merge($config, $this->defaultConfig);

        InstallModel::writeConfigFile($config, $this->getInstallationDir() . 'config.php');

        $this->installed = true;
    }


    public function uninstall()
    {
        if (!$this->isInstalled()) {
            throw new \Exception("system is not installed");
        }

        $fs = new \PhpUnit\Helper\FileSystem2();
        $fs->rm($this->getInstallationDir());
    }

    public function setupUpdate($destinationVersion = null)
    {
        if (!$this->isInstalled()) {
            throw new \Exception("system is not installed");
        }

        if ($destinationVersion === null) {
            $this->setupDevelopmentFiles();
        } else {
            $this->setupPackageFiles($destinationVersion);
        }


        //set test mode
        $configFile = $this->getInstallationDir()."update/Library/Config.php";
        $fh = fopen($configFile, 'a') or die("can't open file");
        $stringData = "\ndefine('IUL_TESTMODE', true);";
        fwrite($fh, $stringData);
        fclose($fh);


        $fs = new \PhpUnit\Helper\FileSystem();
    }

    private function setupDevelopmentFiles()
    {
        $fs = new \PhpUnit\Helper\FileSystem2();
        foreach($folders as $folder) {
            $fs->rm($this->getInstallationDir().$folder);
        }

        $fs = new \PhpUnit\Helper\FileSystem();
        foreach($folders as $folder) {
            $fs->cpDir(TEST_CODEBASE_DIR.$folder, $this->getInstallationDir().$folder);
        }

        $fs->chmod($this->getInstallationDir().$folder, 0777);

    }

    private function setupPackageFiles($destinationVersion)
    {
        $netHelper = new \PhpUnit\Helper\Net();
        $archive = TEST_TMP_DIR.'ImpressPages_'.$destinationVersion.'.zip';
        $migrationModel = new \PhpUnit\Helper\Migration();
        $script = $migrationModel->getScriptToVersion($destinationVersion);
        $netHelper->downloadFile($script->getDownloadUrl(), $archive);

        $fs = new \PhpUnit\Helper\FileSystem2();


        if (!class_exists('PclZip')) {
            require_once(TEST_BASE_DIR.'Helper/PclZip.php');
        }
        $zip = new \PclZip($archive);
        //$status = $zip->extract(PCLZIP_OPT_PATH, $this->getInstallationDir().'update', PCLZIP_OPT_REMOVE_PATH, $this->getSubdir($destinationVersion).'/update');

        if (!$status) {
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


    /**
     * Return MySQL connection to the database
     */
    public function getDbConn()
    {
        throw new \Exception("Not implemented.");
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


    public function putInstallationFiles($destinationDir)
    {
        if ($this->developmentVersion) {
            $this->putInstallationFilesDevelopment($destinationDir);
        } else {
            $this->putInstallationFilesPackage($destinationDir);
        }

        $fs = new FileSystem();
        $fs->chmod($destinationDir, 0777);



    }

    /**
     * Copy current development sources
     * @param string $destination
     */
    private function putInstallationFilesDevelopment($destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination);
        }

        $folders = array(
            'Ip',
            'Plugin',
            'file',
            'install',
            'Theme',
        );

        $files = array(
            'favicon.ico',
            'index.php',
            'license.html',
            'readme.md',
            'robots.txt',
            '.htaccess'
        );


        $fs = new \PhpUnit\Helper\FileSystem();
        foreach($folders as $folder) {
            $fs->cpDir(TEST_CODEBASE_DIR.$folder, $destination.$folder);
            $fs->chmod($destination.$folder, 0777);
        }
        foreach($files as $file) {
            copy(TEST_CODEBASE_DIR.$file, $destination.$file);
            $fs->chmod($destination.$file, 0777);
        }

        file_put_contents($destination . 'config.php', '<?php header("Location: install/"); exit();');
        $fs->chmod($destination . 'config.php', 0777);
    }


    /**
     * Download sources from the internet
     * @param string $destination
     * @throws \Exception
     */
    private function putInstallationFilesPackage($destination)
    {
        $archive = $this->getArchiveFileName($this->getVersion());

        mkdir($destination);

        if (!class_exists('PclZip')) {
            require_once(TEST_BASE_DIR.'Helper/PclZip.php');
        }
        $zip = new \PclZip($archive);
        $success = $zip->extract(PCLZIP_OPT_PATH, $destination, PCLZIP_OPT_REMOVE_PATH, 'ImpressPages');

        if ($success == 0) {
            throw new \Exception("Unrecoverable error: ".$zip->errorInfo(true));
        }

    }
}
