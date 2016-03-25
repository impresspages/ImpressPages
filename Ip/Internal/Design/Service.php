<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Design;


class Service
{

    protected function __construct()
    {
    }

    /**
     * @return Service
     */
    public static function instance()
    {
        return new Service();
    }


    /**
     * @param $themeName
     * @param $filename
     * @return string url to real time compiled less. Available only with admin login.
     */
    public function getRealTimeUrl($themeName, $filename)
    {
        $configModel = ConfigModel::instance();
        $data = array(
            'aa' => 'Design.realTimeLess',
            'file' => $filename,
            'ipDesignPreview' => 1,
            'ipDesign' => array(
                'pCfg' => $configModel->getAllConfigValues($themeName)
            ),
            'rpc' => '2.0'
        );
        if (isset($_GET['theme'])) {
            //for market preview
            $data['theme'] = $_GET['theme'];
        }

        $url = ipConfig()->baseUrl() . '?' . http_build_query($data);
        return $url;
    }

    /**
     * @param string $name
     * @param string $default
     * @param string $themeName
     * @return string
     */
    public function getThemeOption($name, $default = null, $themeName = null)
    {
        if (!$themeName) {
            $themeName = ipConfig()->theme();
        }
        $configModel = ConfigModel::instance();
        $value = $configModel->getConfigValue($themeName, $name, $default);
        return $value;
    }


    public function saveWidgetOptions(Theme $theme)
    {
        $widgetOptions = $theme->getWidgetOptions();
        if (!empty($widgetOptions['image']['width'])) {
            ipSetOption('Content.widgetImageWidth', $widgetOptions['image']['width']);
        }
        if (!empty($widgetOptions['image']['height'])) {
            ipSetOption('Content.widgetImageHeight', $widgetOptions['image']['height']);
        }

        if (!empty($widgetOptions['gallery']['width'])) {
            ipSetOption('Content.widgetGalleryWidth', $widgetOptions['gallery']['width']);
        }
        if (!empty($widgetOptions['gallery']['height'])) {
            ipSetOption('Content.widgetGalleryHeight', $widgetOptions['gallery']['height']);
        }
        if (!empty($widgetOptions['heading']['maxLevel'])) {
            ipSetOption('Content.widgetHeadingMaxLevel', $widgetOptions['heading']['maxLevel']);
        }
    }

    public static function getLayouts()
    {
        return Model::instance()->getThemeLayouts();
    }


    public function getTheme($name = null, $dir = null, $url = null)
    {
        if($name == null) {
            $name = ipConfig()->theme();
        }
        if ($dir == null) {
            $dir = ipFile('Theme/');
        }
        $model = Model::instance();
        $theme = $model->getTheme($name, $dir, $url);
        return $theme;
    }


}
