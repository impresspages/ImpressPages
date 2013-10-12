<?php

namespace Modules\standard\design;


/**
 * Class ThemeDownloader
 * @package Modules\standard\design
 *
 * Downloads and extracts theme into themes directory.
 */
class ThemeDownloader
{
    private $publicKey = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC1iChGp4XVbDk7O6jhMrFpCW0W
vkdcVUCTTo7CD8LBm47m4IW5Q+6OvV8WwrI5COaCr3nJV/AzmjnlVrg+gPRA3rUN
K04RAeg9+OOQ+cTfdlf3koPFbA6Z6Et5+CaiIX5BGBmo18oPIsPobg0NnrZFQens
tf1Tcb4xZFMMKDn/WwIDAQAB
-----END PUBLIC KEY-----';

    public function __construct()
    {
        if (!defined('IP_DESIGN_PHPSECLIB_DIR')) {
            define('IP_DESIGN_PHPSECLIB_DIR', __DIR__.'/phpseclib/');
        }
        require_once IP_DESIGN_PHPSECLIB_DIR.'Crypt/RSA.php';
    }

    public function downloadTheme($name, $url, $signature)
    {
        $model = Model::instance();
        //download theme
        $net = \Library\Php\Net::instance();
        $themeTempFilename = $net->downloadFile($url, BASE_DIR . TMP_SECURE_DIR, $name . '.zip');

        if (!$themeTempFilename) {
            throw new \Ip\CoreException('Theme file download failed.');
        }

        $archivePath = BASE_DIR . TMP_SECURE_DIR . $themeTempFilename;

        //check signature
        $fileMd5 = md5_file($archivePath);

        $rsa = new \Crypt_RSA();
        $rsa->loadKey($this->publicKey);
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $verified = $rsa->verify($fileMd5, base64_decode($signature));

        if (!$verified) {
            throw new \Ip\CoreException('Theme signature verification failed.');
        }

        //extract
        $helper = Helper::instance();
        $tmpExtractedDir = \Library\Php\File\Functions::genUnoccupiedName($name, BASE_DIR . TMP_SECURE_DIR);
        $helper->extractZip(BASE_DIR . TMP_SECURE_DIR . $themeTempFilename, BASE_DIR . TMP_SECURE_DIR . $tmpExtractedDir);
        unlink($archivePath);

        //install
        $extractedDir = $helper->getFirstDir(BASE_DIR . TMP_SECURE_DIR . $tmpExtractedDir);
        $installDir = $model->getThemeInstallDir();
        $newThemeDir = \Library\Php\File\Functions::genUnoccupiedName($name, $installDir);
        rename(BASE_DIR . TMP_SECURE_DIR . $tmpExtractedDir . '/' . $extractedDir, $installDir . $newThemeDir);

    }



}