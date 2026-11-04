<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Seo;


class Model
{
    /**
     * Visible languages with their public pages, in menu order.
     *
     * @return array [['language' => \Ip\Language, 'pages' => array], ...]
     */
    public static function getSitePages()
    {
        $result = [];
        foreach (ipContent()->getLanguages() as $language) {
            if (!$language->isVisible()) {
                continue;
            }
            $result[] = array(
                'language' => $language,
                'pages' => self::getLanguagePages($language),
            );
        }

        return $result;
    }

    /**
     * Public pages of one language: the home page first, then every menu walked depth first.
     * A hidden menu or page hides its whole branch.
     *
     * @param \Ip\Language $language
     * @return array
     */
    public static function getLanguagePages($language)
    {
        $defaultPageId = ipContent()->getDefaultPageId($language->getCode());

        $menus = ipDb()->selectAll(
            'page',
            '*',
            array('languageCode' => $language->getCode(), 'parentId' => 0, 'isDeleted' => 0, 'isVisible' => 1),
            'ORDER BY `pageOrder`'
        );

        $pages = [];
        foreach ($menus as $menu) {
            self::collect($menu['id'], $language, $defaultPageId, $pages);
        }

        // Home page goes first
        usort($pages, function ($a, $b) {
            return $b['isHome'] <=> $a['isHome'];
        });

        return $pages;
    }

    protected static function collect($parentId, $language, $defaultPageId, &$pages)
    {
        $rows = ipDb()->selectAll(
            'page',
            '*',
            array('parentId' => $parentId, 'isDeleted' => 0, 'isVisible' => 1),
            'ORDER BY `pageOrder`'
        );

        foreach ($rows as $row) {
            $isPublic = !$row['isSecured'] && empty($row['redirectUrl']) && (string)$row['urlPath'] !== '';
            if ($isPublic) {
                $isHome = $row['id'] == $defaultPageId;
                $page = new \Ip\Page($row);
                $pages[] = array(
                    'id' => (int)$row['id'],
                    'title' => $row['metaTitle'] ?: $row['title'],
                    'description' => (string)$row['description'],
                    'url' => $isHome ? $language->getLink() : $page->getLink(),
                    'updatedAt' => self::date($row['updatedAt']),
                    'isHome' => $isHome,
                );
            }
            self::collect($row['id'], $language, $defaultPageId, $pages);
        }
    }

    /**
     * @param string|null $timestamp MySQL datetime
     * @return string|null Y-m-d, or null when unknown
     */
    protected static function date($timestamp)
    {
        $time = $timestamp ? strtotime($timestamp) : false;
        return $time && $time > 0 ? date('Y-m-d', $time) : null;
    }
}
