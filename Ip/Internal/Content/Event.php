<?php


namespace Ip\Internal\Content;

use Ip\WidgetController;

class Event
{
    protected static function addWidgetAssets(\Ip\WidgetController $widget)
    {
        $pluginAssetsPath = $widget->getWidgetDir() . \Ip\Application::ASSETS_DIR . '/';
        static::includeResources($pluginAssetsPath);
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
                    ipAddJs($resourcesFolder . $file);
                }
                if (strtolower(substr($file, -4)) == '.css') {
                    ipAddCss($resourcesFolder . $file);
                }
            }
        }
    }

    public static function ipBeforeController()
    {

        $ipUrlOverrides = ipConfig()->getRaw('urlOverrides');
        if (!$ipUrlOverrides) {
            $ipUrlOverrides = array();
        }

        ipAddJsVariable('ipUrlOverrides', $ipUrlOverrides);

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
        Model::removePageRevisions($info['pageId']);
    }



}
