<?php


namespace Ip\Internal\Admin;


class Submenu
{

    public static function getSubmenuItems()
    {
        $submenuItems = ipFilter('ipAdminSystemSubmenu', []);

        if (!$submenuItems) {
            if (ipRoute()->controller() == 'AdminController' && class_exists(ipRoute()->controllerClass())) {
                $submenuItems = self::getControllerMenu(ipRoute()->controllerClass());
            }
        }

        return $submenuItems;
    }

    protected static function getControllerMenu($class)
    {
        $reflector = new \ReflectionClass($class);
        $methods = $reflector->getMethods();

        $submenuItems = [];
        foreach ($methods as $method)
        {
            if (in_array($method, array('index'))) {
                continue;
            }
            if (!$method->isPublic()) {
                continue;
            }
            $docComment = $method->getDocComment();
            if (preg_match_all('/@(\w+)\s+(.*)\r?\n/m', $docComment, $matches)){
                $result = array_combine($matches[1], $matches[2]);
                if (isset($result['ipSubmenu'])) {
                    $menuItem = new \Ip\Menu\Item();
                    $menuItem->setTitle(__($result['ipSubmenu'], ipRoute()->plugin(), false));
                    $menuItem->setUrl(ipActionUrl(array('aa' => ipRoute()->plugin() . '.' . $method->getName())));
                    if (ipRoute()->action() == $method->getName()) {
                        $menuItem->markAsCurrent(true);
                    }
                    $submenuItems[] = $menuItem;
                }
            }
        }
        if (!empty($submenuItems)) {
            return $submenuItems;
        }
        return null;
    }
}
