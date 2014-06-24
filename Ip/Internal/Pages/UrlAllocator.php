<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Pages;


class UrlAllocator
{

    /**
     * Allocate path for new page
     *
     * @param array $page
     */
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

    /**
     * Path available
     *
     * @param string $languageCode
     * @param string $path
     * @return string
     */
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
     * Path available
     *
     * @param string $languageCode
     * @param string $urlPath
     * @return bool
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
