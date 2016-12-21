<?php

/**
 * @package   ImpressPages
 *
 *
 */


namespace Ip\Internal\Update;


class UpdateModel
{


    public function prepareForUpdate()
    {
        new \Ip\Internal\Update\UpdateException(''); //autoload Update exception; As all core files will be deleted soon.
        new \Ip\Response\Json([]); //autoload Json; As all core files will be deleted soon.
        $downloadUrl = ipRequest()->getPost('downloadUrl');
        $md5 = ipRequest()->getPost('md5');

        $fs = new Helper\FileSystem();
        $fs->rm(ipFile('file/tmp/update/extracted/'));

        $this->downloadArchive(
            $downloadUrl,
            $md5,
            ipFile('file/tmp/' . 'update/ImpressPages.zip')
        );
        $this->extractArchive(ipFile('file/tmp/update/ImpressPages.zip'), ipFile('file/tmp/update/extracted/'));

        $backupDir = ipFile('file/tmp/' . date('Y-m-d H.i.s'));
        $fs->rm($backupDir);
        $fs->createWritableDir($backupDir);
        $fs->cpContent(ipFile('Ip/'), $backupDir);
        $fs->makeWritable(ipFile('Ip/'));
        $fs->clean(ipFile('Ip/'));
        $fs->cpContent(ipFile('file/tmp/update/extracted/Ip/'), ipFile('Ip/'));

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
