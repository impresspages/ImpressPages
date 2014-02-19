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
}
