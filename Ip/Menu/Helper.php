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

        $breadcrumb = ipContent()->getBreadcrumb();

        $menuRootId = ipDb()->selectValue('page', 'id', array('alias' => $menuName));

        if ($depthFrom == 1) {
            $elements = ipDb()->selectAll('page', '*', array('isVisible' => 1, 'parentId' => $menuRootId)); //get first level elements
        } elseif (isset($breadcrumb[$depthFrom - 2])) { // if we need a second level (2), we need to find a parent element at first level. And he is at position 0. This is where -2 comes from.
            $elements = ipDb()->selectAll('page', '*', array('isVisible' => 1, 'parentId' => $breadcrumb[$depthFrom - 2]->getId()));
        }

        $items = array();
        if (!empty($elements)) {
            $items = self::arrayToMenuItem($elements, $depthTo, $depthFrom);
        }

        return $items;
    }


    /**
     * Get child items of currently open page or specified page.
     * @param string | null $zoneName zone name
     * @param int | null $elementId
     * @param int $depthTo limit depth of generated menu
     * @return Item[]
     */
    public static function getChildItems($pageId = null, $depthTo = 10000)
    {
        $content = ipContent();
        if ($pageId === null) {
            $page = ipContent()->getCurrentPage();
        } else {
            $page = ipContent()->getPage($pageId);
        }
        if (!$page) {
            return array();
        }


        $pageData = ipDb()->selectAll('page', '*', array('isVisible' => 1, 'parentId' => $page->getId()));
        $items = array();

        if (isset($pageData) && count($pageData) > 0) {
            $curDepth = $page->getDepth();
            $items = self::arrayToMenuItem($pageData, $depthTo + 1, $curDepth);
        }

        return $items;
    }


    /**
     * @param array $pages
     * @param $depth
     * @param $curDepth
     * @return Item[]
     */
    private static function arrayToMenuItem($pages, $depth, $curDepth)
    {
        $items = array();
        foreach ($pages as $pageRow) {
            $page = new \Ip\Page($pageRow['id']);
            $item = new Item();
            $subSelected = false;
            if ($curDepth < $depth) {
                $children = ipDb()->selectAll('page', '*', array('parentId' => $page->getId(), 'isVisible' => 1), 'ORDER BY `pageOrder`');
                if ($children) {
                    $childrenItems = self::arrayToMenuItem($children, $depth, $curDepth + 1);
                    $item->setChildren($childrenItems);
                }
            }
            if ($page->isCurrent() || $page->getType() == 'redirect' && $page->getLink() == \Ip\Internal\UrlHelper::getCurrentUrl()) {
                $item->markAsCurrent(true);
            } elseif ($page->isInCurrentBreadcrumb() || $subSelected || $page->getType() == 'redirect' && self::existInBreadcrumb($page->getLink())) {
                $item->markAsInCurrentBreadcrumb(true);
            }

            $item->setType($page->getType());
            $item->setUrl($page->getLink());
            $item->setTitle($page->getTitle());
            $item->setDepth($curDepth);
            $items[] = $item;
        }

        return $items;
    }


    private static function existInBreadcrumb($link)
    {
        $breadcrumb = ipContent()->getBreadcrumb();
        foreach ($breadcrumb as $element) {
            if ($element->getLink() == $link && $element->getType() != 'redirect' && $element->getType() != 'subpage') {
                return true;
            }
        }

        return false;
    }



}
