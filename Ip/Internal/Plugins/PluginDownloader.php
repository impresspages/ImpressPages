<?php

namespace Ip\Internal\Plugins;


/**
 * Downloads and extracts plugin into plugins directory.
 */
class PluginDownloader
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

    public function downloadPlugin($name, $url, $signature)
    {
        if (is_dir(ipFile("Plugin/{$name}/"))) {
            Service::deactivatePlugin($name);
            Helper::removeDir(ipFile("Plugin/{$name}/"));
        }

        //download plugin
        $net = new \Ip\Internal\NetHelper();
        $pluginTempFilename = $net->downloadFile($url, ipFile('file/secure/tmp/'), $name . '.zip');

        if (!$pluginTempFilename) {
            throw new \Ip\Exception('Plugin file download failed.');
        }

        $archivePath = ipFile('file/secure/tmp/' . $pluginTempFilename);

        //check signature
        $fileMd5 = md5_file($archivePath);

        $rsa = new \Crypt_RSA();
        $rsa->loadKey($this->publicKey);
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        $verified = $rsa->verify($fileMd5, base64_decode($signature));

        if (!$verified) {
            throw new \Ip\Exception('Plugin signature verification failed.');
        }

        //extract
        $secureTmpDir = ipFile('file/secure/tmp/');
        $tmpExtractedDir = \Ip\Internal\File\Functions::genUnoccupiedName($name, $secureTmpDir);
        \Ip\Internal\Helper\Zip::extract($secureTmpDir . $pluginTempFilename, $secureTmpDir . $tmpExtractedDir);
        unlink($archivePath);

        //install
        $extractedDir = $this->getFirstDir($secureTmpDir . $tmpExtractedDir);
        $installDir = Model::pluginInstallDir();
        $newPluginDir = \Ip\Internal\File\Functions::genUnoccupiedName($name, $installDir);
        rename($secureTmpDir . $tmpExtractedDir . '/' . $extractedDir, $installDir . $newPluginDir);

        Service::activatePlugin($name);

    }

    protected function getFirstDir($path)
    {
        $files = scandir($path);
        if (!$files) {
            return false;
        }
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_dir($path . '/' . $file)) {
                return $file;
            }
        }
        return null;
    }


}
