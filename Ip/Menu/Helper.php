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
     * @param string $menuName
     * @param int $depthFrom
     * @param int $depthTo
     * @return Item[]
     */
    public static function getMenuItems($menuName, $depthFrom = 1, $depthTo = 1000)
    {
        return array();

        //variable check
        if ($depthFrom < 1) {
            $backtrace = debug_backtrace();
            if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                trigger_error(
                    '$depthFrom can\'t be less than one. (Error source: ' . $backtrace[0]['file'] . ' line: ' . $backtrace[0]['line'] . ' ) '
                );
            } else {
                trigger_error('$depthFrom can\'t be less than one.');
            }
            return;
        }

        if ($depthTo < $depthFrom) {
            $backtrace = debug_backtrace();
            if (isset($backtrace[0]['file']) && $backtrace[0]['line']) {
                trigger_error(
                    '$depthTo can\'t be lower than $depthFrom. (Error source: ' . $backtrace[0]['file'] . ' line: ' . $backtrace[0]['line'] . ' ) '
                );
            } else {
                trigger_error('$depthTo can\'t be lower than $depthFrom.');
            }
            return;
        }
        //end variable check

        $breadcrumb = static::getBreadcrumb();

        if ($depthFrom == 1) {
            $elements = ipDb(); //get first level elements
        } elseif (isset($breadcrumb[$depthFrom - 2])) { // if we need a second level (2), we need to find a parent element at first level. And he is at position 0. This is where -2 comes from.

            $elements = $zone->getPages(null, $breadcrumb[$depthFrom - 2]->getId());
        }
        $items = array();
        if (isset($elements) && sizeof($elements) > 0) {
            $curDepth = $elements[0]->getDepth();
            $items = self::getSubElementsData($elements, $menuName, $depthTo, $curDepth);
        }

        return $items;
    }


    /**
     * Get child items of currently open page.
     * $zoneName and $elementId should both be defined or leaved blank.
     * @param string | null $zoneName zone name
     * @param int | null $elementId
     * @param int $depthTo limit depth of generated menu
     * @return Item[]
     */
    public static function getChildItems($zoneName = null, $pageId = null, $depthTo = 10000)
    {
        $content = \Ip\ServiceLocator::content();
        if ($zoneName === null || $pageId === null) { //in case zone is set, but elementId is null
            $zoneName = $content->getCurrentZone()->getName();
        }
        if ($pageId === null && $content->getCurrentPage()) {
            $pageId = $content->getCurrentPage()->getId();
        }
        $zone = $content->getZone($zoneName);

        $pages = $zone->getPages(null, $pageId);
        $items = array();
        if (isset($pages) && sizeof($pages) > 0) {
            $curDepth = $pages[0]->getDepth();
            $items = self::getSubElementsData($pages, $zoneName, $depthTo + 1, $curDepth);
        }

        return $items;
    }


    /**
     * @param \Ip\Page[] $pages
     * @param $zoneName
     * @param $depth
     * @param $curDepth
     * @return Item[]
     */
    private static function getSubElementsData($pages, $zoneName, $depth, $curDepth)
    {
        $content = \Ip\ServiceLocator::content();

        $items = array();
        foreach ($pages as $page) {
            $item = new Item();
            $subSelected = false;
            if ($curDepth < $depth) {
                $zone = $content->getZone($zoneName);
                $children = $zone->getPages(null, $page->getId());
                if (sizeof($children) > 0) {
                    $childrenItems = self::getSubElementsData($children, $zoneName, $depth, $curDepth + 1);
                    $item->setChildren($childrenItems);
                }
            }
            if ($page->isCurrent() || $page->getType() == 'redirect' && $page->getLink(
                ) == \Ip\Internal\UrlHelper::getCurrentUrl()
            ) {
                $item->markAsCurrent(true);
            } elseif ($page->isInCurrentBreadcrumb() || $subSelected || $page->getType(
                ) == 'redirect' && self::existInBreadcrumb($page->getLink())
            ) {
                $item->markAsInCurrentBreadcrumb(true);
            }
            $item->setType($page->getType());
            $item->setUrl($page->getLink());
            $item->setTitle($page->getNavigationTitle());
            $item->setDepth($page->getDepth());
            $items[] = $item;
        }

        return $items;
    }


    private static function existInBreadcrumb($link)
    {
        $content = \Ip\ServiceLocator::content();
        $breadcrumb = $content->getBreadcrumb();
        array_pop($breadcrumb);
        foreach ($breadcrumb as $key => $element) {
            if ($element->getLink() == $link && $element->getType() != 'redirect' && $element->getType() != 'subpage') {
                return true;
            }
        }

        if ($link == \Ip\Internal\Deprecated\Url::generate(null, $content->getCurrentZone()->getName())) {
            return true;
        }
        return false;
    }

    public static function getBreadcrumb($pageId = null)
    {
        $pages = array();
        if ($pageId !== null) {
            $page = new \Ip\Page($pageId);
        } else {
            $page = ipCurrentPage()->getPage();
        }

        if ($page) {
            $pages[] = $page;
            $parentPageId = $page->getParentId();
            while (!empty($parentPageId)) {
                $parentPage = new \Ip\Page($parentPageId);
                $pages[] = $parentPage;
                $parentPageId = $parentPage->getParentId();
            }

        }
        return array_reverse($pages);
    }


}
