<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\System;


class Filter {
    public static function ipAdminSystemSubmenu ($menu, $info)
    {
        if (Submenu::isControllerInSystemSubmenu()) {
            $menu = array_merge($menu, Submenu::getSubmenuItems());
            return $menu;
        }

    }

    /**
     * @param \Ip\Menu\Item[] $menu
     * @param $info
     */
    public static function ipAdminMenu ($menu, $info)
    {
        $urls = Submenu::getSubmenuUrls();
        $filteredMenu = array();
        $systemUrl = ipActionUrl(array('aa' => 'System.index'));
        foreach ($menu as $menuItem) {
            if ($menuItem->getUrl() == $systemUrl || !in_array($menuItem->getUrl(), $urls)) {
                $filteredMenu[] = $menuItem;
            }
        }
        return $filteredMenu;
    }
}
