<?php

namespace Ip\Internal\Design;


/**
 * Class ThemeDownloader
 * @package Ip\Internal\Design
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
        if (!defined('IP_PHPSECLIB_DIR')) {
            define('IP_PHPSECLIB_DIR', ipFile('Ip/Lib/phpseclib/'));
        }
        require_once IP_PHPSECLIB_DIR . 'Crypt/RSA.php';
    }

    public function downloadTheme($name, $url, $signature)
    {
        $model = Model::instance();
        //download theme
        $net = new \Ip\Internal\NetHelper();
        $themeTempFilename = $net->downloadFile($url, ipFile('file/secure/tmp/'), $name . '.zip');

        if (!$themeTempFilename) {
            throw new \Ip\Exception('Theme file download failed.');
        }

        $archivePath = ipFile('file/secure/tmp/' . $themeTempFilename);

        //check signature
        $fileMd5 = md5_file($archivePath);

        $rsa = new \Crypt_RSA();
        $rsa->loadKey($this->publicKey);
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $verified = $rsa->verify($fileMd5, base64_decode($signature));

        if (!$verified) {
            throw new \Ip\Exception('Theme signature verification failed.');
        }

        //extract
        $helper = Helper::instance();
        $secureTmpDir = ipFile('file/secure/tmp/');
        $tmpExtractedDir = \Ip\Internal\File\Functions::genUnoccupiedName($name, $secureTmpDir);

        \Ip\Internal\Helper\Zip::extract($secureTmpDir . $themeTempFilename, $secureTmpDir . $tmpExtractedDir);
        unlink($archivePath);

        //install
        $extractedDir = $helper->getFirstDir($secureTmpDir . $tmpExtractedDir);
        $installDir = $model->getThemeInstallDir();
        $newThemeDir = \Ip\Internal\File\Functions::genUnoccupiedName($name, $installDir);
        rename($secureTmpDir . $tmpExtractedDir . '/' . $extractedDir, $installDir . $newThemeDir);
    }

}
