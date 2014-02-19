<?php


namespace Ip\Internal\Admin;


class Submenu
{

    public static function getSubmenuItems()
    {
        $submenuItems = ipFilter('ipAdminSystemSubmenu', array());
        return $submenuItems;
    }
}
