<?php

namespace PhpUnit\Helper;

/**
 * @package ImpressPages
 *
 *
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
    private $cf;
    private $conn;
    private $testDbHelper;

    /**
     * @var true if this installation represents current development version
     */
    private $developmentVersion;

    private $installed;
    /**
     * @param string$version
     */
    public function __construct($version = null)
    {
        if ($version === null) {
            $this->developmentVersion = true;
            $version = \IpUpdate\Library\Service::getLatestVersion();
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

        $this->installed = false;
    }


    /**
     *
     * @param string $version
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

        // INIT CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "");
        curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=xxxxxxxxxxxxxxxxxxxxxxxxxx; path=/' ); //php 5.4 looses session if cookie is not specified (worked fine without that on 5.3


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


        //Put instalation into test mode:
        $configFile = $this->getInstallationDir()."ip_config.php";
        $fh = fopen($configFile, 'a') or die("can't open file");
        $data = "
            define('TEST_MODE', 1);
        ";
        fwrite($fh, $data);
        fclose($fh);

        $fs = new \IpUpdate\Library\Helper\FileSystem();
        $fs->rm($this->getInstallationDir().'update/');
        $fs->rm($this->getInstallationDir().'install/');


        $this->installed = true;
        
    }


    public function uninstall()
    {
        if (!$this->isInstalled()) {
            throw new \Exception("system is not installed");
        }

        $fs = new \IpUpdate\Library\Helper\FileSystem();
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
        $fs->chmod($this->getInstallationDir()."update", 0777);
    }

    private function setupDevelopmentFiles()
    {

        $folders = array(
            'update',
        );


        $fs = new \IpUpdate\Library\Helper\FileSystem();
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
        $status = $zip->extract(PCLZIP_OPT_PATH, $this->getInstallationDir().'update', PCLZIP_OPT_REMOVE_PATH, $this->getSubdir($destinationVersion).'/update');

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
     * 
     * @param string $key configuration value constant
     */
    public function getConfig($key)
    {
        if (!$this->cf) {
            $configurationParser = new \IpUpdate\Library\Model\ConfigurationParser();
            $this->cf = $configurationParser->parse($this->getInstallationDir());
        }
        return $this->cf[$key];
    }
    
    /**
     * Return MySQL connection to the database
     */
    public function getDbConn()
    {
        if (!$this->conn) {
            $connection = mysql_connect($this->getConfig('DB_SERVER'), $this->getConfig('DB_USERNAME'), $this->getConfig('DB_PASSWORD'));
            if ($connection) {
                mysql_select_db($this->getConfig('DB_DATABASE'));
                mysql_query("SET CHARACTER SET ".$this->getConfig('MYSQL_CHARSET'));
                $this->conn = $connection;
            } else {
                throw new \Exception("Can\'t connect to database.");
            }
        }
        return $this->conn;
    }
    
    public function getSubdir($version)
    {
        $oldVersions = array(
            '2.0rc1',
            '2.0rc2',
            '2.0',
            '2.1',
            '2.2',
            '2.3'
        );

        if (in_array($version, $oldVersions)) {
            return 'ImpressPages_'.str_replace('.', '_', $version);
        } else {
            return 'ImpressPages';
        }

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


    private function putInstallationFiles($destinationDir)
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
        mkdir($destination);

        $folders = array(
            'audio',
            'file',
            'image',
            'install',
            'ip_cms',
            'ip_configs',
            'ip_libs',
            'ip_plugins',
            'ip_themes',
            'update',
            'video'
        );

        $files = array(
//            'admin.php',
            'favicon.ico',
            'index.php',
//            'ip_backend_frames.php',
            'ip_backend_worker.php',
            'ip_config.php',
            'ip_cron.php',
            'ip_license.html',
            'readme.md',
            'robots.txt',
            'sitemap.php',
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

        file_put_contents($destination.'robots.txt', '');
        $fs->chmod($destination.'robots.txt', 0777);
        file_put_contents($destination.'ip_config.php',
            '<?php

 if(!isset($_GET[\'install\']))
    header("location: install/?install=1");
        ');
        $fs->chmod($destination.'ip_config.php', 0777);


    }


    /**
     * Download sources from the internet
     * @param string $destination
     * @throws \Exception
     */
    private function putInstallationFilesPackage($destination)
    {
        $netHelper = new \IpUpdate\Library\Helper\Net();
        $archive = TEST_TMP_DIR.'ImpressPages_'.$this->getVersion().'.zip';
        $migrationModel = new \IpUpdate\Library\Model\Migration();
        $script = $migrationModel->getScriptToVersion($this->getVersion());
        $netHelper->downloadFile($script->getDownloadUrl(), $archive);

        mkdir($destination);

        if (!class_exists('PclZip')) {
            require_once(TEST_BASE_DIR.'Helper/PclZip.php');
        }
        $zip = new \PclZip($archive);
        $success = $zip->extract(PCLZIP_OPT_PATH, $destination, PCLZIP_OPT_REMOVE_PATH, $this->getSubdir($this->getVersion()));

        if ($success == 0) {
            throw new \Exception("Unrecoverable error: ".$zip->errorInfo(true));
        }

    }
}