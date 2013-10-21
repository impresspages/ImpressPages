<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Library\Php\Text;

/**
 * replaces special characters in a string
 * @package Library
 */
class Specialchars
{
    /**
     * replace special characters for directory
     * @param $string directory name to replace
     * @return string string without special characters
     */
    public static function dirName($string){
        $new_string = preg_replace("/[^a-zA-Z0-9]/", "_", $string);
        //$new_string = preg_replace("/[^a-zA-Z0-9s]/", "", $string);
        return $new_string;
    }
    /**
     * replace special characters for file name
     * @param $string file name to replace
     * @return string string without special characters
     */
    public static function fileName($string){
        $new_string = preg_replace("/[^.a-zA-Z0-9]/", "_", $string);
        //$new_string = preg_replace("/[^a-zA-Z0-9s]/", "", $string);
        return $new_string;
    }

    public static function url($string){
        require_once (BASE_DIR.LIBRARY_DIR.'php/text/transliteration.php');
        $url = mb_strtolower($string);
        $url = \Library\Php\Text\Transliteration::transform($url);
        $url = str_replace(" ", "-", $url);
        $url = str_replace("/", "-", $url);
        $url = str_replace("\\", "-", $url);
        $url = str_replace("\"", "-", $url);
        $url = str_replace("\'", "-", $url);
        $url = str_replace("„", "-", $url);
        $url = str_replace("“", "-", $url);
        $url = str_replace("&", "-", $url);
        $url = str_replace("%", "-", $url);
        $url = str_replace("`", "-", $url);
        $url = str_replace("!", "-", $url);
        $url = str_replace("@", "-", $url);
        $url = str_replace("#", "-", $url);
        $url = str_replace("$", "-", $url);
        $url = str_replace("^", "-", $url);
        $url = str_replace("*", "-", $url);
        $url = str_replace("(", "-", $url);
        $url = str_replace(")", "-", $url);
        $url = str_replace("{", "-", $url);
        $url = str_replace("}", "-", $url);
        $url = str_replace("[", "-", $url);
        $url = str_replace("]", "-", $url);
        $url = str_replace("|", "-", $url);
        $url = str_replace("~", "-", $url);


        return $url;

    }

}
