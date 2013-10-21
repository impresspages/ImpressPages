<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace PhpUnit\Helper;

class Configuration{

    const CONFIG_FILE_NAME = 'ip_config.php';

    /**
     *
     * Change constant value in ip_config.php file
     * @param stsring $constantName
     * @param string $curValue
     * @param string $newValue
     * @throws \Exception
     */
    public function changeConfigurationConstantValue(\PhpUnit\Helper\Installation $installation, $constantName, $curValue, $newValue) {
        $configFile = $installation->getInstallationDir().self::CONFIG_FILE_NAME;

        if (!is_writable($configFile)) {
            throw new  \Exception("Error: ip_config.php file is not writable. You can make it writable using FTP client or Linux chmod command.");
        }
        $config = file_get_contents($configFile);

        $count;
        $constantName = str_replace('~', '\~', $constantName);
        $curValue = str_replace('~', '\~', $curValue);
        $newValue = str_replace('~', '\~', $newValue);
        $config = preg_replace('~[\'\"]'.$constantName.'[\'\"][ \n]*,[ \n]*[\'\"]'.$curValue.'[\'\"]~s', "'".$constantName."', '".$newValue."'", $config, 1, $count);

        if ($count != 1) {
            throw new \Exception('Can\'t find theme definition in configuration file');
        }

        file_put_contents($configFile, $config);
    }


}