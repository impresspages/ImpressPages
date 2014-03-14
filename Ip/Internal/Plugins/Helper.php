<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Plugins;


use \Ip\Form as Form;

class Helper
{


    /**
     * Clean comments of json content and decode it with json_decode().
     * Work like the original php json_decode() function with the same params
     *
     * @param   string  $json    The json string being decoded
     * @param   bool    $assoc   When TRUE, returned objects will be converted into associative arrays.
     * @param   integer $depth   User specified recursion depth. (>=5.3)
     * @param   integer $options Bitmask of JSON decode options. (>=5.4)
     * @return  string
     */
    public static function jsonCleanDecode($json, $assoc = false, $depth = 512, $options = 0) {

        // search and remove comments like /* */ and //
        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);

        if(version_compare(phpversion(), '5.4.0', '>=')) {
            $json = json_decode($json, $assoc, $depth, $options);
        }
        elseif(version_compare(phpversion(), '5.3.0', '>=')) {
            $json = json_decode($json, $assoc, $depth);
        }
        else {
            $json = json_decode($json, $assoc);
        }

        return $json;
    }

    public static function removeDir($dir, $depth = 0) {

        if (!file_exists($dir)) {
            //already removed
            return true;
        }

        $dir = self::removeTrailingSlash($dir);

        if (!is_writable($dir)) {
            return false;
        }

        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if($file == ".." || $file == ".") {
                        continue;
                    }

                    $result = self::removeDir($dir.'/'.$file, $depth + 1);
                    if (!$result) {
                        return false;
                    }
                }
                closedir($handle);
            }

            $result = rmdir($dir);
            return $result;
        } else {
            $result = unlink($dir);
            return $result;
        }
    }

    private static function removeTrailingSlash($path)
    {
        return preg_replace('{/$}', '', $path);
    }


}