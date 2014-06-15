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

        $i = 2;
        while (!static::isPathAvailable($languageCode, $path . '-' . $i)) {
            $i++;
        }

        return $path . '-' . $i;
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

        if (!$pageId) {
            return true;
        }

        return false;
    }
}
