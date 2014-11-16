<?php
namespace Ip\Internal\Plugins;


class Service
{
    public static function getActivePluginNames()
    {
        return Model::getActivePluginNames();
    }

    public static function getAllPluginNames()
    {
        return Model::getAllPluginNames();
    }

    public static function getActivePlugins()
    {
        return Model::getActivePlugins();
    }

    public static function parsePluginConfigFile($pluginDir)
    {
        return Model::parseConfigFile($pluginDir);
    }

    public static function getPluginConfig($pluginName)
    {
        return Model::getPluginConfig($pluginName);
    }

    public static function activatePlugin($pluginName)
    {
        return Model::activatePlugin($pluginName);
    }

    public static function deactivatePlugin($pluginName)
    {
        return Model::deactivatePlugin($pluginName);
    }

    public static function removePlugin($pluginName)
    {
        return Model::removePlugin($pluginName);
    }


}
