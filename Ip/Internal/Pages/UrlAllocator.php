<?php

namespace Ip\Internal\Pages;

class UrlAllocator
{
    public static function allocatePathForNewPage(array $page)
    {
        if (array_key_exists('urlPath', $page)) {
            $path = $page['urlPath'];
        } elseif (!empty($page['title'])) {
            $path = $page['title'];
        } else {
            $path = 'page';
        }

        $path = \Ip\Internal\Text\Specialchars::url($path);

        return static::allocatePath($page['languageCode'], $path);
    }

    public static function allocatePath($languageCode, $path)
    {
        if (self::isPathAvailable($languageCode, $path)) {
            return $path;
        }

        //temporary remove slash (adding it back at the end)
        $suffix = '';
        if (substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
            $suffix = '/';
        }



        $i = 2;
        //if we have number at the end, start with that number
        $parts = explode('-', strrev($path), 2);
        if (count($parts) == 2 && is_numeric($parts[0])) {
            $i = strrev($parts[0]) + 1;
            $path = strrev($parts[1]);
        }

        while (!static::isPathAvailable($languageCode, $path . '-' . $i)) {
            $i++;
        }

        return $path . '-' . $i . $suffix;
    }

    /**
     * @parem string $languageCode
     * @param $languageCode
     * @param string $urlPath
     * @returns bool true if url is available
     */
    public static function isPathAvailable($languageCode, $urlPath)
    {
        $pageId = ipDb()->selectValue(
            'page',
            '`id`',
            array('languageCode' => $languageCode, 'urlPath' => $urlPath, 'isDeleted' => 0)
        );
        if ($pageId) {
            return false;
        }

        //if lash exists, remove. If there is no slash, add it.
        if (substr($urlPath, -1) == '/') {
            $urlPath = substr($urlPath, 0, -1);
        } else {
            $urlPath .= '/';
        }


        $pageId = ipDb()->selectValue(
            'page',
            '`id`',
            array('languageCode' => $languageCode, 'urlPath' => $urlPath, 'isDeleted' => 0)
        );

        if (!$pageId) {
            return true;
        }

        return false;
    }
}
