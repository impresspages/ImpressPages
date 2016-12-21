<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: maskas
 * Date: 2/19/14
 * Time: 10:17 PM
 */

namespace Ip\Internal\System;


class Submenu
{
    public static function getModuleNames()
    {
        return array('System', 'Administrators', 'Log', 'Email');
    }

    public static function getSubmenuUrls()
    {
        $moduleNames = self::getModuleNames();
        $urls = [];
        foreach ($moduleNames as $moduleName) {
            $urls[] = ipActionUrl(array('aa' => $moduleName . '.index'));
        }

        return $urls;
    }

    protected static function getControllerNames()
    {
        $controllerNames = [];
        foreach (self::getModuleNames() as $name) {
            $controllerNames[] = 'Ip\Internal\\' . $name . '\AdminController';
        }
        return $controllerNames;
    }


    public static function isControllerInSystemSubmenu()
    {
        return in_array(ipRoute()->controllerClass(), self::getControllerNames());
    }

    /**
     * @return \Ip\Menu\Item[]
     */
    public static function getSubmenuItems()
    {
        $modules = self::getModuleNames();

        $submenuItems = [];

        if (0) { // It is for translation engine to find following strings
            __('Content', 'Ip-admin');
            __('Pages', 'Ip-admin');
            __('Design', 'Ip-admin');
            __('Plugins', 'Ip-admin');
            __('Config', 'Ip-admin');
            __('Languages', 'Ip-admin');
            __('System', 'Ip-admin');
        }

        foreach ($modules as $module) {
            $menuItem = new \Ip\Menu\Item();
            $title = $module;
            if ($title == 'Email') {
                $title = 'Email log';
            }
            $menuItem->setTitle(__($title, 'Ip-admin', false)); //
            $menuItem->setUrl(ipActionUrl(array('aa' => $module . '.index')));
            if (ipRoute()->controllerClass() == 'Ip\Internal\\' . $module . '\AdminController') {
                $menuItem->markAsCurrent(true);
            }
            if (ipAdminPermission($module)) {
                $submenuItems[] = $menuItem;
            }
        }
        return $submenuItems;
    }

}
