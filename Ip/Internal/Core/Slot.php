<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Core;


/**
 * class to output current breadcrumb
 * @package ImpressPages
 */
class Slot
{
    public static function breadcrumb_80($params)
    {
        $showHome = isset($params['showHome']) ? $params['showHome'] : true;
        return \Ip\Internal\Breadcrumb\Service::generateBreadcrumb($showHome);
    }

    /**
     * @desc Generate language selection menu
     * @author Allan Laal <allan@permanent.ee>
     * @param array $params
     * @return string
     */
    public static function languages_80($params)
    {
        $data = array(
            'languages' => ipContent()->getLanguages()
        );

        if (!is_array($params)) {
            $params = [];
        }

        $data += $params;

        if (empty($data['attributes']) || !is_array($data['attributes'])) {
            $data['attributes'] = [];
        }

        $data['attributesStr'] = join(
            ' ',
            array_map(
                function ($sKey) use ($data) {
                    if (is_bool($data['attributes'][$sKey])) {
                        return $data['attributes'][$sKey] ? $sKey : '';
                    }
                    return $sKey . '="' . $data['attributes'][$sKey] . '"';
                },
                array_keys($data['attributes'])
            )
        );


        return ipView('Ip/Internal/Config/view/languages.php', $data);
    }

    public static function logo_80()
    {
        $inlineManagementService = new \Ip\Internal\InlineManagement\Service();
        return $inlineManagementService->generateManagedLogo();
    }


    /**
     * @desc Generate menu with custom ul ID and class
     * @author Allan Laal <allan@permanent.ee>
     * @param array $params
     * @return string
     */
    public static function menu_80($params)
    {
        $data = array(
            'items' => null,
            'depth' => 1,
            'active' => 'active',
            'crumb' => 'crumb',
            'disabled' => 'disabled',
            'parent' => 'parent',
            'children' => 'children',
            'view' => 'Ip/Internal/Config/view/menu.php'
        );

        if (is_string($params)) {
            $params = array(
                'items' => $params,
            );
        }

        if (!empty($params[0]) && is_object($params[0]) && $params[0] instanceof \Ip\Menu\Item) {
            $params = array(
                'items' => $params
            );
        }

        $data = array_merge($data, $params); // pass params to View along with other data

        if (isset($params['items']) && is_string($params['items'])) {
            $data['items'] = \Ip\Menu\Helper::getMenuItems($params['items']);
        }
        if (empty($data['attributes']) || !is_array($data['attributes'])) {
            $data['attributes'] = [];
        }

        //generate attributes str
        if (empty($data['attributes']['class'])) {
            $data['attributes']['class'] = '';
        }
        $data['attributes']['class'] = 'level' . $data['depth'] . ' ' . $data['attributes']['class'];
        $data['attributesStr'] = join(
            ' ',
            array_map(
                function ($sKey) use ($data) {
                    if (is_bool($data['attributes'][$sKey])) {
                        return $data['attributes'][$sKey] ? $sKey : '';
                    }
                    return $sKey . '="' . $data['attributes'][$sKey] . '"';
                },
                array_keys($data['attributes'])
            )
        );

        $view = ipView($data['view'], $data);
        return $view->render();
    }


    public static function text_80($params)
    {
        $tag = 'div';
        $defaultValue = '';
        $cssClass = '';
        $attributes = null;
        $attributesStr = '';
        if (empty($params['id'])) {
            throw new \Ip\Exception("Ip.text slot requires parameter 'id'");
        }
        $key = $params['id'];

        if (isset($params['tag'])) {
            $tag = $params['tag'];
        }

        if (isset($params['default'])) {
            $defaultValue = $params['default'];
        }

        if (isset($params['class'])) {
            $cssClass = $params['class'];
        }

        if (isset($params['attributes'])) {
            $attributes = $params['attributes'];
        }

        $inlineManagementService = new \Ip\Internal\InlineManagement\Service();
        return $inlineManagementService->generateManagedText($key, $tag, $defaultValue, $cssClass, $attributes);
    }


    public static function image_80($params)
    {
        $options = [];
        $defaultValue = '';
        $cssClass = '';
        if (empty($params['id'])) {
            throw new \Ip\Exception("Ip.image slot requires parameter 'id'");
        }
        $key = $params['id'];

        if (isset($params['default'])) {
            $defaultValue = $params['default'];
        }

        if (isset($params['width'])) {
            $options['width'] = $params['width'];
        }
        if (isset($params['height'])) {
            $options['height'] = $params['height'];
        }

        if (isset($params['class'])) {
            $cssClass = $params['class'];
        }

        $inlineManagementService = new \Ip\Internal\InlineManagement\Service();
        return $inlineManagementService->generateManagedImage($key, $defaultValue, $options, $cssClass);
    }
}
