<?php

/**
 * @package   ImpressPages
 *
 *
 */


namespace Modules\administrator\system;



class UpdateModel
{

    public function prepareForUpdate()
    {

        $updateVersionInfo = $this->getUpdateInfo();

        if (!$updateVersionInfo) {
            throw new UpdateException('Can\'t find update archive');
        }

        $this->downloadArchive($updateVersionInfo['downloadUrl'], $updateVersionInfo['md5'], BASE_DIR.TMP_FILE_DIR.'update/ImpressPages.zip');
        $this->extractArchive(BASE_DIR.TMP_FILE_DIR.'update/ImpressPages.zip', BASE_DIR.TMP_FILE_DIR.'update/extracted/');

        $fs = new Helper\FileSystem();
        $fs->rm(BASE_DIR.'update');
        $fs->createWritableDir(BASE_DIR.'update/extracted/update');
        $fs->clean(BASE_DIR.'update/extracted/update');
        $fs->cpContent(BASE_DIR.TMP_FILE_DIR.'update/extracted/update', BASE_DIR.'update');
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

        $zip = new \PclZip($archivePath);
        $status = $zip->extract(PCLZIP_OPT_PATH, $extractedPath, PCLZIP_OPT_REMOVE_PATH, 'ImpressPages');

        if (!$status) {
            throw new UpdateException("Archive extraction failed");
        }

    }

    private function getUpdateInfo()
    {
        if (!function_exists('curl_init')) {
            throw new UpdateException('CURL extension required');
        }

        $ch = curl_init();

        $curVersion = \DbSystem::getSystemVariable('version');

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we dont timeout
            CURLOPT_URL => \Modules\administrator\system\Model::instance()->getImpressPagesAPIUrl(),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'module_group=service&module_name=communication&action=getUpdateInfo&curVersion='.$curVersion
        );

        curl_setopt_array($ch, $options);

        $jsonAnswer = curl_exec($ch);

        $answer = json_decode($jsonAnswer, true);

        if ($answer === null || !isset($answer['status']) || $answer['status'] != 'success') {
            return false;
        }

        return $answer;
    }


}

