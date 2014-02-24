<?php

namespace Ip\Internal\Pages;

class UrlAllocator
{
    public static function slugify($string)
    {
        $string = mb_strtolower($string);
        $string = \Ip\Internal\Text\Transliteration::transform($string);

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
            "'" => "",
            "?" => "",
            ":" => "",
            ";" => "",
        );
        $string = strtr($string, $replace);

        $string = preg_replace('/-+/', '-', $string);

        return $string;
    }

    public static function allocatePathForNewPage(array $page)
    {
        if (array_key_exists('urlPath', $page)) {
            $path = $page['urlPath'];
        } elseif (!empty($page['title'])) {
            $path = $page['title'];
        } else {
            $path = 'page';
        }

        $path = static::slugify($path);

        return static::allocatePath($page['languageCode'], $path);
    }

    public static function allocatePath($languageCode, $path)
    {
        if (self::isPathAvailable($languageCode, $path)) {
            return $path;
        }

        $i = 2;
        while (!self::isPathAvailable($languageCode, $path . '-' . $i)) {
            $i++;
        }

        return $path . '-' . $i;
    }

    /**
     * @param string $urlPath
     * @param int $allowed_id
     * @returns bool true if url is available ignoring $allowed_id page.
     */
    public static function isPathAvailable($urlPath, $allowedId = null)
    {

        $pageId = ipDb()->selectValue('page', '`id`', array('urlPath' => $urlPath));

        if (!$pageId) {
            return true;
        }

        if ($allowedId && $pageId == $allowedId) {
            return true;
        }

        return false;
    }

    public static function ensureUniqueUrl($url, $allowedId = null)
    {
        $url = str_replace("/", "-", $url);

        if (self::isPathAvailable($url, $allowedId))
            return $url;

        $i = 2;
        while (!self::isPathAvailable($url . '-' . $i, $allowedId)) {
            $i++;
        }

        return $url . '-' . $i;
    }

}
