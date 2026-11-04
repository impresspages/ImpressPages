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

    /**
     * AI crawlers that can be blocked from robots.txt: user agent => who runs it.
     *
     * @return array
     */
    public static function aiCrawlers()
    {
        return array(
            'GPTBot' => 'OpenAI',
            'ClaudeBot' => 'Anthropic',
            'PerplexityBot' => 'Perplexity',
            'Google-Extended' => 'Google Gemini',
            'CCBot' => 'Common Crawl',
        );
    }

    /**
     * Config option name for an AI crawler, e.g. "Google-Extended" => "aiCrawlerGoogleExtended".
     *
     * @param string $userAgent
     * @return string
     */
    public static function aiCrawlerOption($userAgent)
    {
        return 'aiCrawler' . preg_replace('/[^A-Za-z0-9]/', '', $userAgent);
    }

    /**
     * Address fields of the organisation behind the website: option name => schema.org PostalAddress property.
     *
     * @return array
     */
    public static function addressFields()
    {
        return array(
            'organizationStreetAddress' => 'streetAddress',
            'organizationLocality' => 'addressLocality',
            'organizationRegion' => 'addressRegion',
            'organizationPostalCode' => 'postalCode',
            'organizationCountry' => 'addressCountry',
        );
    }

    /**
     * URL of the website logo when an image logo is set, otherwise null.
     *
     * @return string|null
     */
    public static function logoUrl()
    {
        $dao = new \Ip\Internal\InlineManagement\Dao();
        $logo = new \Ip\Internal\InlineManagement\Entity\Logo($dao->getGlobalValue(\Ip\Internal\InlineManagement\Dao::PREFIX_LOGO, ''));
        if ($logo->getType() !== \Ip\Internal\InlineManagement\Entity\Logo::TYPE_IMAGE || !$logo->getImage()) {
            return null;
        }
        return ipFileUrl($logo->getImage());
    }

    /**
     * Readable text of a page's published content, with headings and list items kept as Markdown.
     *
     * @param int $pageId
     * @return string
     */
    public static function pageText($pageId)
    {
        $revision = ipDb()->selectRow(
            'revision',
            'revisionId',
            array('pageId' => $pageId, 'isPublished' => 1),
            'ORDER BY `createdAt` DESC, `revisionId` DESC'
        );
        if (!$revision) {
            return '';
        }

        // Every block of the revision, "main" (the page body in every theme) first.
        // Widgets of a page revision carry languageId 0; the language only applies to blocks shared across pages.
        $widgets = ipDb()->selectAll(
            'widget',
            '*',
            array('revisionId' => $revision['revisionId'], 'isDeleted' => 0),
            "ORDER BY `blockName` = 'main' DESC, `blockName`, `position`"
        );

        $available = \Ip\Internal\Content\Model::getAvailableWidgetObjects();
        $html = '';
        foreach ($widgets as $widget) {
            if (empty($available[$widget['name']])) {
                continue;
            }
            $data = json_decode((string)$widget['data'], true);
            try {
                $html .= $available[$widget['name']]->generateHtml($widget['revisionId'], $widget['id'], is_array($data) ? $data : [], $widget['skin']) . "\n";
            } catch (\Throwable $e) {
                // A widget that can't render outside a page view is left out
                continue;
            }
        }

        return self::htmlToText($html);
    }

    /**
     * @param string $html
     * @return string
     */
    public static function htmlToText($html)
    {
        $html = preg_replace('#<(script|style|noscript|form|iframe|svg)\b.*?</\1>#is', '', $html);
        $html = preg_replace_callback('#<h([1-6])\b[^>]*>(.*?)</h\1>#is', function ($match) {
            return "\n\n" . str_repeat('#', min(6, (int)$match[1] + 2)) . ' ' . trim(strip_tags($match[2])) . "\n\n";
        }, $html);
        $html = preg_replace('#<li\b[^>]*>#i', "\n- ", $html);
        $html = preg_replace('#<br\s*/?>#i', "\n", $html);
        $html = preg_replace('#</(p|div|ul|ol|table|tr|blockquote|section|article)>#i', "\n\n", $html);
        $html = preg_replace('#</t[dh]>#i', ' ', $html);

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xC2\xA0", ' ', $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/ *\n */', "\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        $text = preg_replace("/^- *\n/m", '', $text);
        $text = preg_replace("/^(- .*)\n\n(?=- )/m", "$1\n", $text);

        return trim($text);
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
