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
        $downloadUrl = ipRequest()->getPost('downloadUrl');
        $md5 = ipRequest()->getPost('md5');

        $updateVersionInfo = $this->getUpdateInfo();

        if (!$updateVersionInfo) {
            throw new UpdateException('Can\'t find update archive');
        }

        $this->downloadArchive(
            $downloadUrl,
            $md5,
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

    public function getUpdateInfo()
    {
        if (!function_exists('curl_init')) {
            return false;
        }

        $ch = curl_init();

        $curVersion = \Ip\ServiceLocator::storage()->get('Ip', 'version');

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we dont timeout
            CURLOPT_URL => \Ip\Internal\System\Model::instance()->getImpressPagesAPIUrl(),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'module_name=communication&action=getUpdateInfo&curVersion=' . $curVersion
        );

        curl_setopt_array($ch, $options);

        $jsonAnswer = curl_exec($ch);

        $answer = json_decode($jsonAnswer, true);

        if ($answer === null || !isset($answer['status']) || $answer['status'] != 'success') {
            return false;
        }

        return $answer;
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
