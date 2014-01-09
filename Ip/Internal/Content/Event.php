<?php


namespace Ip\Internal\Content;


class Event
{
    public static function ipInitFinished()
    {
        // Add widgets
        //TODO cache found assets to decrease file system usage
        $widgets = Service::getAvailableWidgets();

        if (ipIsManagementState()) {
            foreach ($widgets as $widget) {
                if (!$widget->isCore()) { //core widget assets are included automatically in one minified file
                    static::addWidgetAssets($widget);
                }
            }
        }

    }

    protected static function addWidgetAssets(\Ip\WidgetController $widget)
    {
        $pluginAssetsPath = $widget->getModuleName() . '/' . Model::WIDGET_DIR . '/' . $widget->getName(
            ) . '/' . \Ip\Application::ASSETS_DIR . '/';
        if ($widget->isCore()) {
            $widgetPublicDir = 'Ip/Internal/' . $pluginAssetsPath;
        } else {
            $widgetPublicDir = 'Plugin/' . $pluginAssetsPath;
        }


        static::includeResources($widgetPublicDir);
    }

    private static function includeResources($resourcesFolder)
    {

        if (is_dir(ipFile($resourcesFolder))) {
            $files = scandir(ipFile($resourcesFolder));
            if ($files === false) {
                return;
            }


            foreach ($files as $file) {
                if (is_dir(ipFile($resourcesFolder . $file)) && $file != '.' && $file != '..') {
                    static::includeResources(ipFile($resourcesFolder . $file));
                    continue;
                }
                if (strtolower(substr($file, -3)) == '.js') {
                    ipAddJs(ipFileUrl($resourcesFolder . '/' . $file));
                }
                if (strtolower(substr($file, -4)) == '.css') {
                    ipAddCss(ipFileUrl($resourcesFolder . '/' . $file));
                }
            }
        }
    }



} 