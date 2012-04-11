<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\standard\configuration;

if (!defined('CMS')) exit;


class Model{

    const CONFIG_FILE_NAME = 'ip_config.php';
    
    /**
     * 
     * Change constant value in ip_config.php file
     * @param stsring $constantName
     * @param string $curValue
     * @param string $newValue
     * @throws \Exception
     */
    public function changeConfigurationConstantValue($constantName, $curValue, $newValue) {
        if (!is_writable(BASE_DIR.self::CONFIG_FILE_NAME)) {
            throw new  \Exception("Error: ip_config.php file is not writable. You can make it writable using FTP client or Linux chmod command.");
        }
        $config = file_get_contents(BASE_DIR.self::CONFIG_FILE_NAME);

        $count;
        $config = preg_replace('/[\'\"]'.$constantName.'[\'\"][ \n]*,[ \n]*[\'\"]'.$curValue.'[\'\"]/s', "'".$constantName."', '".$newValue."'", $config, 1, $count);

        if ($count != 1) {
            throw new \Exception('Can\'t find theme definition in configuration file');
        }
        
        file_put_contents(BASE_DIR.self::CONFIG_FILE_NAME, $config);
    }
}