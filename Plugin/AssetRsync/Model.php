<?php

namespace Plugin\AssetRsync;

class Model
{
    protected static function getAssetDestinationDirectory()
    {
        return rtrim(ipGetOption('AssetRsync.assetDestinationDirectory'). '/\\') . '/';
    }

    public static function syncAssets()
    {
        $directoryIterator = new \DirectoryIterator(ipFile('Ip/Module'));

        foreach ($directoryIterator as $directory) {
            static::syncPlugin('Ip/Module', $directory->getFilename());
        }

        $directoryIterator = new \DirectoryIterator(ipFile('Plugin'));

        foreach ($directoryIterator as $directory) {
            static::syncPlugin('Plugin', $directory->getFilename());
        }
    }

    public static function syncPlugin($parentDirectory, $pluginName)
    {
        $assetsDir = ipFile("$parentDirectory/$pluginName/assets");
        if (file_exists($assetsDir)) {
            static::syncDir($assetsDir, static::getAssetDestinationDirectory() . $pluginName . "/assets");
        }

        $widgetDir = ipFile("$parentDirectory/$pluginName/Widget");
        if (file_exists($widgetDir)) {
            $directoryIterator = new \DirectoryIterator($widgetDir);
            foreach ($directoryIterator as $directory) {
                static::syncWidget($parentDirectory, $pluginName, $directory->getFilename());
            }
        }
    }

    protected static function syncWidget($parentDirectory, $pluginName, $widgetName)
    {
        $assetsDir = ipFile("$parentDirectory/$pluginName/Widget/$widgetName/assets");
        if (file_exists($assetsDir)) {
            static::syncDir($assetsDir, static::getAssetDestinationDirectory() . $pluginName . '/Widget/$widgetName/assets');
        }
    }

    protected static function syncDir($sourceDir, $destinationDir)
    {
        system("mkdir -p $destinationDir");
        system("rsync -a $sourceDir/ $destinationDir/");
    }
}
