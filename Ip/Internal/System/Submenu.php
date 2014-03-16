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


class Submenu {
    public static function getModuleNames()
    {
        return array('System', 'Administrators', 'Log', 'Email');
    }

    public static function getSubmenuUrls()
    {
        $moduleNames = self::getModuleNames();
        $urls = array();
        foreach ($moduleNames as $moduleName) {
            $urls[] = ipActionUrl(array('aa' => $moduleName . '.index'));
        }

        return $urls;
    }

    protected static function getControllerNames()
    {
        $controllerNames = array();
        foreach (self::getModuleNames() as $name) {
            $controllerNames[] = 'Ip\Internal\\' . $name . '\AdminController';
        }
        return $controllerNames;
    }


    public static function isControllerInSystemSubmenu()
    {
        return in_array(ipRequest()->getControllerClass(), self::getControllerNames());
    }

    public static function getSubmenuItems()
    {
        $modules = self::getModuleNames();

        $submenuItems = array();

        foreach ($modules as $module) {
            $menuItem = new \Ip\Menu\Item();
            $menuItem->setTitle(__($module, 'ipAdmin', FALSE));
            $menuItem->setUrl(ipActionUrl(array('aa' => $module . '.index')));
            if (ipRequest()->getControllerClass() == 'Ip\Internal\\' . $module . '\AdminController') {
                $menuItem->markAsCurrent(TRUE);
            }
            if (ipAdminPermission($module)) {
                $submenuItems[] = $menuItem;
            }
        }
        return $submenuItems;
    }

}
