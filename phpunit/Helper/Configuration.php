<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace PhpUnit\Helper;

class Configuration{

    const CONFIG_FILE_NAME = 'config.php';

    /**
     *
     * Change constant value in config.php file
     * @param stsring $constantName
     * @param string $curValue
     * @param string $newValue
     * @throws \Exception
     */
    public function changeConfigurationValues(\PhpUnit\Helper\Installation $installation, $newValues) {
        $configFile = $installation->getInstallationDir().self::CONFIG_FILE_NAME;

        if (!is_writable($configFile)) {
            throw new  \Exception("Error: config.php file is not writable. You can make it writable using FTP client or Linux chmod command.");
        }

        $config = include $configFile;

        foreach ($newValues as $key => $value) {
            $config[$key] = $value;
        }

        \Ip\Internal\Install\Model::writeConfigFile($config, $configFile);
    }


}