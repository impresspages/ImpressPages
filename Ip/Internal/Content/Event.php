<?php


namespace Ip\Internal\Content;

use Ip\WidgetController;

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
        $pluginAssetsPath = $widget->getWidgetDir() . \Ip\Application::ASSETS_DIR . '/';
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
                    ipAddJs($resourcesFolder . '/' . $file);
                }
                if (strtolower(substr($file, -4)) == '.css') {
                    ipAddCss($resourcesFolder . '/' . $file);
                }
            }
        }
    }

    public static function ipInit()
    {
        ipAddJs('Ip/Internal/Content/assets/widgets.js');

        $ipUrlOverrides = ipConfig()->getRaw('URL_OVERRIDES');
        if (!$ipUrlOverrides) {
            $ipUrlOverrides = array();
        }

        ipAddJsVariable('ipUrlOverrides', $ipUrlOverrides);
    }

    public static function ipAdminLoginSuccessful($data)
    {
        Service::setManagementMode(1);
    }

    public static function ipCronExecute($info)
    {
        if ($info['firstTimeThisDay'] || $info['test']) {
            Model::deleteUnusedWidgets();
        }
    }

    public static function ipPageRevisionDuplicated($info)
    {
        Model::duplicateRevision($info['basedOn'], $info['newRevisionId']);
    }


    public static function ipPageRevisionRemoved($info)
    {
        Model::removeRevision($info['revisionId']);
    }

    public static function ipPageRevisionPublished($info)
    {
        Model::clearCache($info['revisionId']);
    }

    public static function ipPageDeleted($info)
    {
        Model::removePageRevisions($info['zoneName'], $info['pageId']);
    }

    public static function ipPageMoved($info)
    {
        //TODOXX THIS EVENT IS NEVER THROWN #150
        if ($info['newZoneName'] != $info['oldZoneName']) {
            //move revisions from one zone to another
            Model::updatePageRevisionsZone($info['pageId'], $info['oldZoneName'], $info['newZoneName']);
        }
    }

}
