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
class Slot {
    public static function breadcrumb_80($params)
    {
        $showHome = isset($params['showHome']) ? $params['showHome'] : true;
        return \Ip\Internal\Breadcrumb\Service::generateBreadcrumb(' &rsaquo; ', $showHome);
    }

	/**
	 * @desc Generate language selection menu
	 * @author Allan Laal <allan@permanent.ee>
	 * @param array $params
	 * @example <br>echo \Ip\Internal\Core\Slot::languages_80(array(<br>'ul' => array(<br>		'id'	=> 'langmenu',<br>		'class'	=> 'floatlist right clearfix',<br>	),<br>	'li' => array(<br>		'class'			=> 'some classes to prepend to all lis', // active/inactive will still work<br>	)<br>));
	 * 
	 * @return string
	 */
    public static function languages_80($params)
    {
        if(!ipGetOption('Config.multilingual')) {
            return '';
        }

        return ipView('Ip/Internal/Config/view/languages.php', array('attributes' => $params, 'languages' => ipContent()->getLanguages()));
    }

    public static function logo_80()
    {
        $inlineManagementService = new \Ip\Internal\InlineManagement\Service();
        return $inlineManagementService->generateManagedLogo();
    }

    public static function menu_80($items)
    {
        if (is_string($items)) {
            $items = \Ip\Menu\Helper::getMenuItems($items);
        }
        $data = array(
            'items' => $items,
            'depth' => 1
        );

        $viewFile = ipFile('Ip/Internal/Config/view/menu.php');
        $view = ipView($viewFile, $data);
        return $view->render();
    }


    public static function text_80($params)
    {
        $tag = 'div';
        $defaultValue = '';
        $cssClass = '';
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

        $inlineManagementService = new \Ip\Internal\InlineManagement\Service();
        return $inlineManagementService->generateManagedText($key, $tag, $defaultValue, $cssClass);
    }



    public static function image_80($params)
    {
        $options = array();
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
