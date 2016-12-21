<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\System;


class Filter
{
    public static function ipAdminSystemSubmenu($menu, $info)
    {
        if (Submenu::isControllerInSystemSubmenu()) {
            $menu = array_merge($menu, Submenu::getSubmenuItems());
            return $menu;
        }
        return null;
    }

    /**
     * @param \Ip\Menu\Item[] $menu
     * @param $info
     */
    public static function ipAdminMenu($menu, $info)
    {
        $urls = Submenu::getSubmenuUrls();
        $filteredMenu = [];

        //remove menu items that are in submenu
        foreach ($menu as $menuItem) {
            if (!in_array($menuItem->getUrl(), $urls)) {
                $filteredMenu[] = $menuItem;
            }
        }


        $submenuItems = Submenu::getSubmenuItems();
        if (!empty($submenuItems)) {
            $firstSubmenuItem = $submenuItems[0];
            $newItem = new \Ip\Internal\Admin\MenuItem();
            $newItem->setTitle(__('System', 'Ip-admin', false));
            $newItem->setUrl($firstSubmenuItem->getUrl());
            $newItem->setIcon('fa-cogs');
            $filteredMenu[] = $newItem;
        }

        return $filteredMenu;
    }
}
