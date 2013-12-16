<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\Install;



class OptionHelper
{

    public static function import($configFile)
    {
        $content = file_get_contents($configFile);
        $values = json_decode($content, true);
        if (!is_array($values)) {
            throw new \IpUpdate\Library\UpdateException("Can't parse configuration file: " . $configFile, \IpUpdate\Library\UpdateException::SQL);
        }
        foreach ($values as $key => $value) {
            ipSetOption($key, $value);
        }
    }



}