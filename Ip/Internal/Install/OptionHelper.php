<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Install;



class OptionHelper
{

    public static function import($configFile)
    {
        $content = file_get_contents($configFile);
        $values = json_decode($content, true);
        if (!is_array($values)) {
            throw new \Exception("Can't parse configuration file: " . $configFile);
        }
        foreach ($values as $key => $value) {
            ipSetOption($key, $value);
        }
    }



}
