<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Menu;


/**
 *
 * Get menu item arrays for menu generation
 *
 */
class Helper
{

    /**
     * Get specific levels of menu.
     *
     * Common usage:
     * Get items of a first menu level (to display on top of the site), e.g., $result = \Ip\Menu\Helper::getMenuItems('menu1', 1, 1);
     * Get 7 levels of children of selected page on top menu (to display on a side): $result = \Ip\Menu\Helper::getMenuItems('menu1', 2,  7);
     *
     * Pass the result to ipSlot('menu', array('items' => $result));
     *
     * Please note, that items of a child level can only be returned if a selected page is in a breadcrumb parent page.
     * In opposite case, the function returns an empty array.
     *
     * @param string $menuName eg menu1
     * @param int $depthFrom
     * @param int $depthTo
     * @param string $orderBy can be set to 'title' to change ordering
     * @return array
     * @throws \Ip\Exception
     */
    public static function getMenuItems($menuName, $depthFrom = 1, $depthTo = 1000, $orderBy = null)
    {
        if ($orderBy == 'title') {
            $order = '`title`';
        } else {
            $order = '`pageOrder`';
        }

        // variable check
        if ($depthFrom < 1) {
            $backtrace = debug_backtrace();
            if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                throw new \Ip\Exception(
                    '$depthFrom can\'t be less than one. (Error source: ' . $backtrace[0]['file'] . ' line: ' . $backtrace[0]['line'] . ' ) '
                );
            } else {
                throw new \Ip\Exception ('$depthFrom can\'t be less than one.');
            }
        }

        if ($depthTo < $depthFrom) {
            $backtrace = debug_backtrace();
            if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                throw new \Ip\Exception(
                    '$depthTo can\'t be lower than $depthFrom. (Error source: ' . $backtrace[0]['file'] . ' line: ' . $backtrace[0]['line'] . ' ) '
                );
            } else {
                throw new \Ip\Exception('$depthTo can\'t be lower than $depthFrom.');
            }
        }
        // end variable check

        $breadcrumb = ipContent()->getBreadcrumb();

        $languageCode = ipContent()->getCurrentLanguage()->getCode();

        $menuRootId = ipDb()->selectValue(
            'page',
            'id',
            array('languageCode' => $languageCode, 'alias' => $menuName, 'isDeleted' => 0)
        );

        if ($depthFrom == 1) {
            $elements = ipDb()->selectAll(
                'page',
                '*',
                array('isVisible' => 1, 'isSecured' => 0, 'parentId' => $menuRootId, 'isDeleted' => 0),
                "ORDER BY $order"
            ); //get first level elements
        } elseif (isset($breadcrumb[$depthFrom - 2])) { // if we need a second level (2), we need to find a parent element at first level. And it is at position 0. This is where -2 comes from.
            if (!empty($breadcrumb[0])) {
                $rootPage = ipContent()->getPage($breadcrumb[0]->getParentId());
                if ($rootPage && $rootPage->getAlias() == $menuName) {
                    $parent = $breadcrumb[$depthFrom - 2];
                    $elements = ipDb()->selectAll(
                        'page',
                        '*',
                        array('isVisible' => 1, 'isSecured' => 0, 'parentId' => $parent->getId(), 'isDeleted' => 0),
                        "ORDER BY $order"
                    );
                } else {
                    $elements = array();
                }
            } else {
                $elements = array();
            }
        }

        $items = array();
        if (!empty($elements)) {
            $items = self::arrayToMenuItem($elements, $depthTo, $depthFrom, $order);
        }

        return $items;
    }

    /**
     * Get children items of current or specified page.
     *
     * @param int|string $pageId pageId or an alias
     * @param int $depthLimit limit the depth of items to be returned
     * @param string $orderBy can be set to 'title' to change ordering
     * @return array
     */
    public static function getChildItems($pageId = null, $depthLimit = 1000, $orderBy = null)
    {
        if ($orderBy == 'title') {
            $order = '`title`';
        } else {
            $order = '`pageOrder`';
        }

        if (is_string($pageId) && !ctype_digit($pageId)) {
            // $pageId is an alias. Get the real id;
            $pageId = ipContent()->getPage($pageId)->getId();
            if (!$pageId) {
                return array();
            }
        }
        if ($pageId === null) {
            $pageId = ipContent()->getCurrentPage()->getId();
        }

        $elements = ipDb()->selectAll(
            'page',
            '*',
            array('isVisible' => 1, 'isSecured' => 0, 'parentId' => $pageId, 'isDeleted' => 0),
            "ORDER BY $order"
        ); //get first level elements

        $items = array();
        if (!empty($elements)) {
            $items = self::arrayToMenuItem($elements, $depthLimit, 1, $order);
        }

        return $items;
    }

    /**
     * @param array $pages
     * @param int $depth
     * @param int $curDepth
     * @param string $order
     * @return array
     */
    private static function arrayToMenuItem($pages, $depth, $curDepth, $order)
    {
        $items = array();
        foreach ($pages as $pageRow) {
            $page = new \Ip\Page($pageRow['id']);
            $item = new Item();
            $subSelected = false;
            if ($curDepth < $depth) {
                $children = ipDb()->selectAll(
                    'page',
                    '*',
                    array('parentId' => $page->getId(), 'isVisible' => 1, 'isSecured' => 0, 'isDeleted' => 0),
                    "ORDER BY $order"
                );
                if ($children) {
                    $childrenItems = self::arrayToMenuItem($children, $depth, $curDepth + 1, $order);
                    $item->setChildren($childrenItems);
                }
            }
            if ($page->isCurrent() || $page->getRedirectUrl() && $page->getLink(
                ) == \Ip\Internal\UrlHelper::getCurrentUrl()
            ) {
                $item->markAsCurrent(true);
            } elseif ($page->isInCurrentBreadcrumb() || $subSelected || $page->getRedirectUrl(
                ) && self::existInBreadcrumb($page->getLink())
            ) {
                $item->markAsInCurrentBreadcrumb(true);
            }

            if ($page->isDisabled()) {
                $item->setUrl('');
            } elseif ($page->getRedirectUrl()) {
                $url = $page->getRedirectUrl();
                if (!preg_match('/^((http|https):\/\/|\/\/)/i', $url)) {
                    $url = ipConfig()->protocol() . '://' . $url;
                }
                $item->setUrl($url);
            } else {
                $item->setUrl($page->getLink());
            }
            $metaTitle = $page->getMetaTitle();
            $item->setBlank($page->isBlank());
            $item->setTitle($page->getTitle());
            $item->setDepth($curDepth);
            $item->setDisabled($page->isDisabled());
            $item->setId($page->getId());
            $item->setAlias($page->getAlias());
            $item->setPageTitle(empty ($metaTitle) ? $page->getTitle() : $metaTitle);
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param string $link
     * @return bool
     */
    private static function existInBreadcrumb($link)
    {
        $breadcrumb = ipContent()->getBreadcrumb();
        foreach ($breadcrumb as $element) {
            if ($element->getLink() == $link && !$element->getRedirectUrl()) {
                return true;
            }
        }

        return false;
    }

}
