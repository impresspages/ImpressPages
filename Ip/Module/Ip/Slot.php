<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Ip;


/**
 * class to ouput current breadcrumb
 * @package ImpressPages
 */
class Slot {
    public static function breadcrumb($params)
    {
        $showHome = isset($params['showHome']) ? $params['showHome'] : true;
        return \Ip\Module\Breadcrumb\Model::generateBreadcrumb(' &rsaquo; ', $showHome);
    }

    public static function languages($params)
    {
        return \Ip\Module\Languages\Model::generateLanguageList();
    }

    public static function logo()
    {
        $inlineManagementService = new \Ip\Module\InlineManagement\Service();
        return $inlineManagementService->generateManagedLogo();
    }

    public static function menu($items)
    {
        if (is_string($items)) {
            $items = \Ip\Menu\Helper::getZoneItems($items);
        }
        $data = array(
            'items' => $items,
            'depth' => 1
        );

        $viewFile = ipConfig()->coreModuleFile('Config/view/menu.php');
        $view = \Ip\View::create($viewFile, $data);
        return $view->render();
    }
}