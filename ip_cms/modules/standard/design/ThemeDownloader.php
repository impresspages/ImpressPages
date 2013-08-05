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
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR . 'phpseclib');
        require_once 'Crypt/RSA.php';
    }

    public function downloadTheme($name, $url, $signature)
    {
        $net = \Library\Php\Net::instance();
        $themeTempFilename = $net->downloadFile($url, BASE_DIR . TMP_FILE_DIR, $name . '.zip');

        if (!$themeTempFilename) {
            throw new \Ip\CoreException('Theme file download failed.');
        }

        $archivePath = BASE_DIR . TMP_FILE_DIR . $themeTempFilename;

        $fileMd5 = md5_file($archivePath);

        $rsa = new \Crypt_RSA();
        $rsa->loadKey($this->publicKey);
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $verified = $rsa->verify($fileMd5, base64_decode($signature));

        if (!$verified) {
            throw new \Ip\CoreException('Theme signature verification failed.');
        }

        $this->extractZip(BASE_DIR . TMP_FILE_DIR . $themeTempFilename, BASE_DIR . THEME_DIR);

        unlink($archivePath);
    }

    private function extractZip($archivePath, $destinationDir)
    {
        if (class_exists('\\ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($archivePath) === TRUE) {
                $zip->extractTo($destinationDir);
                $zip->close();
            } else {
                throw new \Ip\CoreException('Theme extraction failed.');
            }
        } else {
            $zip = new \PclZip($archivePath);
            if (!$zip->extract(PCLZIP_OPT_PATH, $destinationDir)) {
                throw new \Ip\CoreException('Theme extraction failed.');
            }
        }
    }
}