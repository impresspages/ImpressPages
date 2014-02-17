<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Library\Model;


class Update
{

    private $cf;

    const STEP_START = 0;
    const STEP_DOWNLOAD_PACKAGE = 1;
    const STEP_CLOSE_WEBSITE = 2;
    const STEP_REMOVE_OLD_FILES = 3;
    const STEP_RUN_MIGRATIONS = 4;
    const STEP_WRITE_NEW_FILES = 5;
    const STEP_PUBLISH_WEBSITE = 6;
    const STEP_FINISH = 7;

    const UPDATE_SCRIPT_VERSION = '4.0';


    /**
     * @var \IpUpdate\Library\Model\TempStorage
     */
    private $tempStorage;

    /**
     *
     * @var \IpUpdate\Library\Migration\General
     */
    private $destinationScript;

    /**
     *
     * @var \IpUpdate\Library\Helper\FileSystem
     */
    private $fs;

    public function __construct($config)
    {
        $this->cf = $config;
        $this->tempStorage = new \IpUpdate\Library\Model\TempStorage($this->cf['BASE_DIR'].$this->cf['tmpFileDir'].'update/');
        $this->fs = new \IpUpdate\Library\Helper\FileSystem();

        $updateModel = new \IpUpdate\Library\Model\Migration();
        $this->destinationScript = $updateModel->getDestinationScript($this->getCurrentVersion());
    }

    public function getCurrentVersion()
    {
        $db = new \IpUpdate\Library\Model\Db();
        $dbh = $db->connect($this->cf);

        $sql = '
            SELECT
                value
            FROM
                ' . ipTable('variables') . '
            WHERE
                `name` = :name
        ';

        $params = array (
            ':name' => 'version'
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);

        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            $answer = $lock['value'];
            return $answer;
        } else {
            throw new \IpUpdate\Library\UpdateException("Can't find installation version ".$sql, \IpUpdate\Library\UpdateException::SQL);
        }
    }

    /**
     *
     * @param int $destinationStep - step after which script should terminate
     * @throws \IpUpdate\Library\UpdateException
     * @throws \IpUpdate\Library\Options
     * @throws \IpUpdate\Library\Exception
     */
    public function proceed($destinationStep = self::STEP_FINISH, \IpUpdate\Library\Options $options = null)
    {
        if (!$this->destinationScript) {
            throw new \IpUpdate\Library\UpdateException("No update available", \IpUpdate\Library\UpdateException::NO_UPDATE);
        }

        if ($this->tempStorage->exist('inProgress')) {
            //existing inProgress variable means that some step is in progress at the moment or has failed.
            throw new \IpUpdate\Library\UpdateException("Update is in progress", \IpUpdate\Library\UpdateException::IN_PROGRESS);
        }

        if (!$this->tempStorage->exist('version')) {
            $this->tempStorage->setValue('curStep', self::STEP_START);
            $this->tempStorage->setValue('version', self::UPDATE_SCRIPT_VERSION);
        }

        if ($this->tempStorage->getValue('version') != self::UPDATE_SCRIPT_VERSION) {
            //if script version value is wrong, some other script is executing the update. Should be very rare case.
            throw new \IpUpdate\Library\UpdateException("Update is in progress", \IpUpdate\Library\UpdateException::IN_PROGRESS);
        }

        if ($this->tempStorage->getValue('version') === false) {
            $this->tempStorage->setValue('curStep', self::STEP_START);
            $this->tempStorage->setValue('version', self::UPDATE_SCRIPT_VERSION);
        }
        $curStep = (int)$this->tempStorage->getValue('curStep');
        if ($curStep > $destinationStep) {
            $this->stepFinish($options);
        }

        $this->tempStorage->setValue('inProgress', 1);
        try {
            while ($curStep <= $destinationStep) {
                switch($curStep) {
                    case self::STEP_START:
                            $this->stepStart($options);
                        break;
                    case self::STEP_DOWNLOAD_PACKAGE:
                            $this->stepDownloadPackage($options);
                        break;
                    case self::STEP_CLOSE_WEBSITE:
                            $this->stepCloseWebsite($options);
                        break;
                    case self::STEP_REMOVE_OLD_FILES:
                            $this->stepRemoveOldFiles($options);
                        break;
                    case self::STEP_RUN_MIGRATIONS:
                            $this->stepRunMigrations($options);
                        break;
                    case self::STEP_WRITE_NEW_FILES:
                            $this->stepWriteNewFiles($options);
                        break;
                    case self::STEP_PUBLISH_WEBSITE:
                            $this->stepPublishWebsite($options);
                        break;
                    case self::STEP_FINISH:
                            $this->stepFinish($options);
                        break;

                    default:

                        throw new \IpUpdate\Library\Exception("Unknown update state.");
                        break;
                }
                $this->tempStorage->setValue('curStep', $curStep + 1);
                $curStep = $this->tempStorage->getValue('curStep');
            }
        } catch (\IpUpdate\Library\UpdateException $e) {
            $this->tempStorage->remove('inProgress');
            throw $e;
        }

        $this->tempStorage->remove('inProgress');
    }

    public function resetLock()
    {
        $this->cleanUp();
    }


    private function stepStart(\IpUpdate\Library\Options $options = null)
    {
        //do nothing
    }

    private function stepDownloadPackage(\IpUpdate\Library\Options $options = null)
    {
        if ($options && $options->ignoreFiles()) {
            return;
        }

        $archivePath = $this->getNewArchivePath();
        $scriptUrl = $this->destinationScript->getDownloadUrl();

        if (file_exists($archivePath)) {
            if (md5_file($archivePath) == $this->destinationScript->getMd5()) {
                return; //everything is fine. We have the right archive in place
            } else {
                //archive checksum is wrong. Remove the archive;
                $this->fs->rm($archivePath);
            }
        }

        //download archive
        $downloadHelper = new \IpUpdate\Library\Helper\Net();
        $downloadHelper->downloadFile($scriptUrl, $archivePath);
        if (md5_file($archivePath) != $this->destinationScript->getMd5()) {
            throw new \IpUpdate\Library\UpdateException("Downloaded archive doesn't mach md5 checksum", \IpUpdate\Library\UpdateException::WRONG_CHECKSUM);
        }
    }

    private function stepCloseWebsite(\IpUpdate\Library\Options $options = null)
    {
        if ($options && $options->ignoreFiles()) {
            return;
        }

        $indexFile = $this->cf['baseDir'].'index.php';
        $this->fs->makeWritable($indexFile);
        $maintenanceMode = '<?php
header("HTTP/1.1 503 Service Temporarily Unavailable");
header("Status: 503 Service Temporarily Unavailable");
header("Retry-After: 3600");

if (file_exists(__DIR__.\'/maintenance.php\')) {
    require(__DIR__.\'/maintenance.php\');
}
';

        file_put_contents($indexFile, $maintenanceMode);


    }

    private function stepRemoveOldFiles(\IpUpdate\Library\Options $options = null)
    {

        if ($options && $options->ignoreFiles()) {
            return;
        }

        if (file_exists($this->cf['baseDir'].'install') || is_dir($this->cf['baseDir'].'install')) {
            $this->fs->rm($this->cf['baseDir'].'install');
        }

        $replaceFolders = $this->getFoldersToReplace();
        $replaceFiles = $this->getFilesToReplace();
        foreach($replaceFolders as $folder) {
            $this->fs->makeWritable($this->cf['baseDir'].$folder);
            $this->fs->clean($this->cf['baseDir'].$folder); //just clean the content to leave writable folder
        }


        foreach($replaceFiles as $file) {
            if (file_exists($this->cf['baseDir'].$file)) {
                $this->fs->makeWritable($this->cf['baseDir'].$file);
                file_put_contents($this->cf['baseDir'].$file, '');
            }
        }

        if (file_exists($this->cf['baseDir'].'admin.php')) {
            $this->fs->makeWritable($this->cf['baseDir'].'admin.php');
            unlink($this->cf['baseDir'].'admin.php');
        }
        if (file_exists($this->cf['baseDir'].'ip_backend_frames.php')) {
            $this->fs->makeWritable($this->cf['baseDir'].'ip_backend_frames.php');
            unlink($this->cf['baseDir'].'ip_backend_frames.php');
        }

    }


    private function stepRunMigrations(\IpUpdate\Library\Options $options = null)
    {
        $updateModel = new \IpUpdate\Library\Model\Migration();
        $scripts = $updateModel->getScriptsFromVersion($this->getCurrentVersion());

        foreach ($scripts as $script) {
            $script->process($this->cf);
        }

    }

    private function stepWriteNewFiles(\IpUpdate\Library\Options $options = null)
    {
        if ($options && $options->ignoreFiles()) {
            return;
        }

        $archivePath = $this->getNewArchivePath();
        $extractedPath = $this->getExtactedNewArchivePath();

        $this->fs->createWritableDir($extractedPath);
        $this->fs->clean($extractedPath);

        if (!class_exists('PclZip')) {
            require_once(IUL_BASE_DIR . 'Helper/PclZip.php');
        }
        $zip = new \PclZip($archivePath);
        if(function_exists('set_time_limit')) {
            set_time_limit(90);
        }
        $status = $zip->extract(PCLZIP_OPT_PATH, $extractedPath, PCLZIP_OPT_REMOVE_PATH, $this->getSubdir($this->destinationScript->getDestinationVersion()));

        if (!$status) {
            throw new \IpUpdate\Library\UpdateException("Archive extraction failed. Error: $status", \IpUpdate\Library\UpdateException::EXTRACT_FAILURE);
        }

        $replaceFolders = $this->getFoldersToReplace();
        $replaceFiles = $this->getFilesToReplace();
        foreach($replaceFolders as $folder) {
            $this->fs->cpContent($extractedPath.$folder, $this->cf['baseDir'].$folder);
        }

        foreach($replaceFiles as $file) {
            if (file_exists($this->cf['baseDir'].$file)) {
                unlink($this->cf['baseDir'].$file);
            }
            copy($extractedPath.$file, $this->cf['baseDir'].$file);
        }
    }

    private function stepPublishWebsite(\IpUpdate\Library\Options $options = null)
    {
        if ($options && $options->ignoreFiles()) {
            return;
        }
        $extractedPath = $this->getExtactedNewArchivePath();
        unlink($this->cf['baseDir'].'index.php');
        copy($extractedPath.'index.php', $this->cf['baseDir'].'index.php');
    }

    private function stepFinish(\IpUpdate\Library\Options $options = null)
    {
        $this->cleanUp();
        $this->setVersion($this->destinationScript->getDestinationVersion());
        $this->increaseCacheNumber();
    }

    private function cleanUp()
    {
        $this->tempStorage->remove('inProgress');
        $this->tempStorage->remove('curStep');
        $this->tempStorage->remove('version');
        $this->fs->clean($this->cf['baseDir'].$this->cf['tmpFileDir'].'update/');
    }

    private function getFoldersToReplace()
    {
        return array (
            'Ip/'
        );
    }

    private function getFilesToReplace()
    {
        return array (
            'license.html',
            'sitemap.php',
            $this->cf['fileDir'].'.htaccess',
            $this->cf['fileDir'].'secure/.htaccess'
        );
    }

    private function getNewArchivePath()
    {
        $dir = $this->cf['baseDir'].$this->cf['tmpFileDir'].'update/';
        $this->fs->createWritableDir($dir);
        return $dir.'ImpressPages.zip';
    }

    private function getExtactedNewArchivePath()
    {
        $dir = $this->cf['baseDir'].$this->cf['tmpFileDir'].'update/extracted/';
        return $dir;
    }

    public function getSubdir($version)
    {
        return 'ImpressPages';
    }

    /**
     *
     * @param string $version
     * @throws Exception
     */
    private function setVersion($version)
    {
        $db = new \IpUpdate\Library\Model\Db();
        $dbh = $db->connect($this->cf);

        $sql = '
            UPDATE
                ' . ipTable('variables') . '
            SET
                `value` = :version
            WHERE
                `name` = :name
        ';

        $params = array (
            ':version' => $version,
            ':name' => 'version'
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);

        if ($this->getCurrentVersion() != $version){
            throw new \IpUpdate\Library\UpdateException("Can't update system version to: ".$version, \IpUpdate\Library\UpdateException::UNKNOWN);
        }
    }

    private function increaseCacheNumber()
    {
        $db = new \IpUpdate\Library\Model\Db();
        $dbh = $db->connect($this->cf);

        $sql = '
            UPDATE
                ' . ipTable('variables') . '
            SET
                `value` = `value` + 1
            WHERE
                `name` = :name
        ';

        $params = array (
            ':name' => 'cache_version'
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }
}
