<?php

/**
 * @package   ImpressPages
 *
 *
 */


namespace Ip\Internal\Update;


class UpdateModel
{
    public static function runMigrations()
    {

    }

    public function prepareForUpdate()
    {

        $updateVersionInfo = $this->getUpdateInfo();

        if (!$updateVersionInfo) {
            throw new UpdateException('Can\'t find update archive');
        }

        $this->downloadArchive(
            $updateVersionInfo['downloadUrl'],
            $updateVersionInfo['md5'],
            ipFile('file/tmp/' . 'update/ImpressPages.zip')
        );
        $this->extractArchive(ipFile('file/tmp/update/ImpressPages.zip'), ipFile('file/tmp/update/extracted/'));

        $fs = new Helper\FileSystem();
        $backupDir = file('file/tmp/' . date('Y-m-d H.i.s'));
        $fs->rm($backupDir);
        $fs->createWritableDir($backupDir);
        $fs->cpContent(ipFile('Ip'), $backupDir);
        $fs->clean(ipFile('Ip'));
        $fs->cpContent(ipFile('file/tmp/update/extracted/Ip'), ipFile('Ip'));

    }


    private function downloadArchive($scriptUrl, $md5checksum, $archivePath)
    {

        if (file_exists($archivePath)) {
            if (md5_file($archivePath) == $md5checksum) {
                return; //everything is fine. We have the right archive in place
            } else {
                //archive checksum is wrong. Remove the archive;
                unlink($archivePath);
            }
        }

        //download archive
        $downloadHelper = new Helper\Net();
        $downloadHelper->downloadFile($scriptUrl, $archivePath);
        if (md5_file($archivePath) != $md5checksum) {
            throw new UpdateException("Downloaded archive doesn't mach md5 checksum");
        }
    }

    private function extractArchive($archivePath, $extractedPath)
    {
        $fs = new Helper\FileSystem();
        $fs->createWritableDir($extractedPath);
        $fs->clean($extractedPath);

        require_once(ipFile('Ip/Internal/PclZip.php'));
        $zip = new \PclZip($archivePath);
        $status = $zip->extract(PCLZIP_OPT_PATH, $extractedPath, PCLZIP_OPT_REMOVE_PATH, 'ImpressPages');

        if (!$status) {
            throw new UpdateException("Archive extraction failed");
        }

    }

}
