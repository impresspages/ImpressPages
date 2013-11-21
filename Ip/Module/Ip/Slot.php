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


    public static function text ($params)
    {
        $tag = 'div';
        $defaultValue = '';
        $cssClass = '';
        if (empty($params['id'])) {
            throw new \Ip\CoreException("Ip.text slot requires parameter 'key'");
        }
        $key = $params['id'];

        if (isset($params['tag'])) {
            $tag = $params['tag'];
        }

        if (isset($params['default'])) {
            $defaultValue = $params['default'];
        }

        if (isset($params['css'])) {
            $cssClass = $params['css'];
        }

        $inlineManagementService = new \Ip\Module\InlineManagement\Service();
        return $inlineManagementService->generateManagedText($key, $tag, $defaultValue, $cssClass);
    }

    public static function string($params)
    {
        $tag = 'p';
        $defaultValue = '';
        $cssClass = '';
        if (empty($params['id'])) {
            throw new \Ip\CoreException("Ip.text slot requires parameter 'key'");
        }
        $key = $params['id'];

        if (isset($params['tag'])) {
            $tag = $params['tag'];
        }

        if (isset($params['default'])) {
            $defaultValue = $params['default'];
        }

        if (isset($params['css'])) {
            $cssClass = $params['css'];
        }
        $inlineManagementService = new \Ip\Module\InlineManagement\Service();
        return $inlineManagementService->generateManagedString($key, $tag, $defaultValue, $cssClass);
    }

    public static function image ($key, $defaultValue = null, $options = array(), $cssClass = null)
    {
        $inlineManagementService = new \Ip\Module\InlineManagement\Service();
        return $inlineManagementService->generateManagedImage($key, $defaultValue, $options, $cssClass);
    }

}