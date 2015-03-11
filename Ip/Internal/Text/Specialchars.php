<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Text;

/**
 * replaces special characters in a string
 * @package Library
 */
class Specialchars
{
    /**
     * replace special characters for directory
     * @param string $string directory name to replace
     * @return string string without special characters
     */
    public static function dirName($string)
    {
        $new_string = preg_replace("/[^a-zA-Z0-9]/", "_", $string);
        //$new_string = preg_replace("/[^a-zA-Z0-9s]/", "", $string);
        return $new_string;
    }

    /**
     * replace special characters for file name
     * @param string $string file name to replace
     * @return string string without special characters
     */
    public static function fileName($string)
    {
        $new_string = preg_replace("/[^.a-zA-Z0-9]/", "_", $string);
        //$new_string = preg_replace("/[^a-zA-Z0-9s]/", "", $string);
        return $new_string;
    }

    public static function url($string)
    {
        $url = mb_strtolower($string);
        $url = \Ip\Internal\Text\Transliteration::transform($url);
        $replace = array(
            " " => "-",
            "/" => "-",
            "\\" => "-",
            "\"" => "-",
            "\'" => "-",
            "„" => "-",
            "“" => "-",
            "&" => "-",
            "%" => "-",
            "`" => "-",
            "!" => "-",
            "@" => "-",
            "#" => "-",
            "$" => "-",
            "^" => "-",
            "*" => "-",
            "(" => "-",
            ")" => "-",
            "{" => "-",
            "}" => "-",
            "[" => "-",
            "]" => "-",
            "|" => "-",
            "~" => "-",
            "." => "-",
            "+" => "-",
            "'" => "-",
            "?" => "-",
            ":" => "-",
            ";" => "-",
        );
        $url = strtr($url, $replace);

        $url = preg_replace('/-+/', '-', $url);

        return $url;

    }

}
